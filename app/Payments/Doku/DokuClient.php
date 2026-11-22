<?php

namespace App\Payments\Doku;

use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Calls to DOKU: open a Checkout payment page, and ask for a payment's status. Settings: config('services.doku').
 *
 * Fake mode (DOKU_FAKE=true, only with APP_ENV=local): nothing is sent to DOKU. The payment page is
 * FakeDokuController's test page on this website, and the status comes from that page's choice.
 */
class DokuClient
{
    public const CHECKOUT_PATH = '/checkout/v1/payment';

    /** Credentials the fake mode signs with when no real keys are set. */
    private const FAKE_CLIENT_ID = 'FAKE-DOKU-CLIENT';
    private const FAKE_SECRET_KEY = 'fake-doku-secret-key';

    public function __construct(private array $config)
    {
    }

    public static function fromConfig(): self
    {
        return new self(config('services.doku'));
    }

    /** Fake mode, and only on a developer's PC (or in the automated tests). */
    public function isFake(): bool
    {
        return !empty($this->config['fake']) && app()->environment('local', 'testing');
    }

    public function isConfigured(): bool
    {
        if (empty($this->config['enabled'])) {
            return false;
        }

        return $this->isFake() || (!empty($this->config['client_id']) && !empty($this->config['secret_key']));
    }

    public function clientId(): string
    {
        return (string) ($this->config['client_id'] ?: ($this->isFake() ? self::FAKE_CLIENT_ID : ''));
    }

    public function secretKey(): string
    {
        return (string) ($this->config['secret_key'] ?: ($this->isFake() ? self::FAKE_SECRET_KEY : ''));
    }

    /**
     * Open a DOKU Checkout page. Returns DOKU's "response" part (payment.url is the page to send the customer to).
     *
     * @param  array  $order  order fields (amount, invoice_number, callback_url_result ...); currency is added here
     */
    public function createCheckout(array $order, array $customer = [], ?string $notificationUrl = null): array
    {
        if ($this->isFake()) {
            return ['payment' => [
                'url'          => route('payments.fake-doku.show', $order['invoice_number']),
                'token_id'     => 'fake-' . Str::random(10),
                'expired_date' => now('Asia/Jakarta')->addMinutes($this->config['due_minutes'] ?: 60)->format('YmdHis'),
            ]];
        }

        $body = [
            'order' => $order + [
                'currency'              => $this->config['currency'],
                'language'              => 'EN',
                'auto_redirect'         => true,
                'disable_retry_payment' => false,
            ],
            'payment' => array_filter([
                'payment_due_date'     => $this->config['due_minutes'] ?: 60,
                'payment_method_types' => array_values($this->config['methods'] ?? []) ?: null,
            ]),
        ];
        if ($customer) {
            $body['customer'] = $customer;
        }
        if ($notificationUrl) {
            // This order's notifications come here, whatever is set in the DOKU Back Office
            $body['additional_info'] = ['override_notification_url' => $notificationUrl];
        }

        $response = $this->send('POST', self::CHECKOUT_PATH, $body);
        $url = data_get($response, 'response.payment.url');
        if (!$url) {
            throw new RuntimeException('DOKU did not return a payment page: ' . json_encode(data_get($response, 'error_messages', $response)));
        }

        return $response['response'];
    }

    /** Status of an order by its invoice number: the same shape as a notification (transaction.status ...). */
    public function status(string $invoiceNumber): array
    {
        if ($this->isFake()) {
            // What was chosen on the fake page; until then, still waiting
            $transaction = PaymentTransaction::where('reference', $invoiceNumber)->first();

            return [
                'order'       => ['invoice_number' => $invoiceNumber, 'amount' => (float) ($transaction->amount ?? 0)],
                'transaction' => ['status' => $transaction->payload['fake_status'] ?? 'PENDING', 'date' => now('UTC')->format('Y-m-d\TH:i:s\Z')],
            ];
        }

        return $this->send('GET', '/orders/v1/status/' . rawurlencode($invoiceNumber));
    }

    private function send(string $method, string $path, ?array $body = null): array
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('DOKU is not set up (DOKU_ENABLED, DOKU_CLIENT_ID, DOKU_SECRET_KEY).');
        }

        // The signature covers the exact bytes sent, so the body is encoded once and sent as it is
        $json = $body === null ? null : json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $requestId = (string) Str::uuid();
        $timestamp = now('UTC')->format('Y-m-d\TH:i:s\Z');

        $request = Http::timeout(30)->acceptJson()->withHeaders([
            'Client-Id'         => $this->clientId(),
            'Request-Id'        => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature'         => DokuSignature::make($this->clientId(), $requestId, $timestamp, $path, $json, $this->secretKey()),
        ]);
        $url = rtrim($this->config['base_url'], '/') . $path;

        $response = $method === 'GET' ? $request->get($url) : $request->withBody($json, 'application/json')->post($url);
        if ($response->failed()) {
            throw new RuntimeException("DOKU $path answered {$response->status()}: " . $response->body());
        }

        return $response->json() ?? [];
    }
}
