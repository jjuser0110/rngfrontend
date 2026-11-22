<?php

namespace App\Payments\Doku;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/** Calls to DOKU: open a Checkout payment page, and ask for a payment's status. Settings: config('services.doku'). */
class DokuClient
{
    public const CHECKOUT_PATH = '/checkout/v1/payment';

    public function __construct(private array $config)
    {
    }

    public static function fromConfig(): self
    {
        return new self(config('services.doku'));
    }

    public function isConfigured(): bool
    {
        return !empty($this->config['enabled']) && !empty($this->config['client_id']) && !empty($this->config['secret_key']);
    }

    /**
     * Open a DOKU Checkout page. Returns DOKU's "response" part (payment.url is the page to send the customer to).
     *
     * @param  array  $order  order fields (amount, invoice_number, callback_url_result ...); currency is added here
     */
    public function createCheckout(array $order, array $customer = [], ?string $notificationUrl = null): array
    {
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
            'Client-Id'         => $this->config['client_id'],
            'Request-Id'        => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature'         => DokuSignature::make($this->config['client_id'], $requestId, $timestamp, $path, $json, $this->config['secret_key']),
        ]);
        $url = rtrim($this->config['base_url'], '/') . $path;

        $response = $method === 'GET' ? $request->get($url) : $request->withBody($json, 'application/json')->post($url);
        if ($response->failed()) {
            throw new RuntimeException("DOKU $path answered {$response->status()}: " . $response->body());
        }

        return $response->json() ?? [];
    }
}
