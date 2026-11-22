<?php

namespace App\Payments;

use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\Reservation;
use App\Models\User;
use App\Payments\Doku\DokuClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * The customer pays a booking's security deposit online with DOKU.
 *
 * Each attempt is a payment_transactions row (gateway "doku", reference = the DOKU invoice number). Only DOKU's
 * signed notification, or DOKU's own status answer, marks it paid. That records the deposit as a Payment
 * ("Deposit / Authorization", Approved) that staff see in rngcarrental, once only.
 */
class DepositPayment
{
    public const GATEWAY = 'doku';
    public const METHOD = 'Online Payment (DOKU)';

    public function __construct(private DokuClient $doku)
    {
    }

    public static function enabled(): bool
    {
        return DokuClient::fromConfig()->isConfigured();
    }

    /** What is still to pay on the deposit (0 when paid or not needed). */
    public static function outstanding(Reservation $reservation): float
    {
        return max(0, round((float) $reservation->security_deposit - (float) $reservation->securityDeposit()->sum('amount'), 2));
    }

    /** The "Pay deposit" button shows for this booking. */
    public static function canPay(Reservation $reservation): bool
    {
        return self::enabled()
            && in_array($reservation->status, config('services.doku.pay_statuses', []), true)
            && self::outstanding($reservation) > 0;
    }

    /** DOKU payment page for this booking's deposit: an open one is reused, else a new one is made. */
    public function start(Reservation $reservation, User $user): string
    {
        $amount = self::outstanding($reservation);

        $open = $reservation->paymentTransactions()
            ->where('gateway', self::GATEWAY)->where('status', 'pending')->latest('id')->get()
            ->first(fn ($transaction) => abs((float) $transaction->amount - $amount) < 0.01 && $this->stillOpen($transaction));
        if ($open) {
            return $open->payload['payment_url'];
        }

        $transaction = PaymentTransaction::create([
            'reservation_id' => $reservation->id,
            'gateway'        => self::GATEWAY,
            'amount'         => $amount,
            'currency'       => config('services.doku.currency'),
            'status'         => 'pending',
            'payload'        => ['user_id' => $user->id],
        ]);
        // Letters, digits and dashes only, under 30 characters (DOKU's limit with card payments)
        $transaction->update(['reference' => 'DEP-' . $reservation->id . '-' . $transaction->id]);

        $resultUrl = route('payments.doku.result', [$reservation, $transaction]);
        try {
            $response = $this->doku->createCheckout(
                [
                    'amount'              => self::dokuAmount($amount),
                    'invoice_number'      => $transaction->reference,
                    'callback_url'        => $resultUrl,
                    'callback_url_result' => $resultUrl,
                    'line_items'          => [[
                        'id'       => 'deposit-' . $reservation->id,
                        'name'     => 'Security deposit for booking ' . ($reservation->code ?? $reservation->id),
                        'price'    => self::dokuAmount($amount),
                        'quantity' => 1,
                    ]],
                ],
                array_filter([
                    'id'    => (string) $reservation->customer_id,
                    'name'  => $reservation->customer->full_name ?? null,
                    'email' => $reservation->customer->email ?? null,
                ]),
                route('payments.doku.notify'),
            );
        } catch (\Throwable $e) {
            $transaction->update(['status' => 'error', 'payload' => $transaction->payload + ['error' => $e->getMessage()]]);
            throw $e;
        }

        $transaction->update(['payload' => $transaction->payload + [
            'payment_url'  => data_get($response, 'payment.url'),
            'token_id'     => data_get($response, 'payment.token_id'),
            'expired_date' => data_get($response, 'payment.expired_date'),
        ]]);

        return $transaction->payload['payment_url'];
    }

    /**
     * Apply what DOKU says about a payment (its notification, or its status answer).
     * SUCCESS with the right amount records the deposit once; EXPIRED closes the attempt; anything else waits
     * (with Checkout a FAILED card can still be paid another way on the same page).
     */
    public function apply(array $data, string $source): ?PaymentTransaction
    {
        $invoice = (string) data_get($data, 'order.invoice_number');
        $status = strtoupper((string) data_get($data, 'transaction.status'));

        return DB::transaction(function () use ($data, $invoice, $status, $source) {
            $transaction = PaymentTransaction::where('gateway', self::GATEWAY)->where('reference', $invoice)->lockForUpdate()->first();
            if (!$transaction) {
                Log::warning('DOKU: unknown invoice number', ['invoice' => $invoice, 'source' => $source]);

                return null;
            }

            $history = $transaction->payload['history'] ?? [];
            $history[] = ['at' => now()->toDateTimeString(), 'source' => $source, 'status' => $status, 'data' => $data];
            $payload = ['history' => array_slice($history, -10)] + $transaction->payload;

            if ($transaction->status === 'paid' || $status !== 'SUCCESS') {
                $transaction->update(['payload' => $payload] + ($status === 'EXPIRED' && $transaction->status === 'pending' ? ['status' => 'expired'] : []));

                return $transaction;
            }

            if (abs((float) data_get($data, 'order.amount') - (float) $transaction->amount) >= 0.01) {
                Log::error('DOKU: paid amount differs from the deposit asked', ['invoice' => $invoice, 'paid' => data_get($data, 'order.amount'), 'asked' => $transaction->amount]);
                $transaction->update(['status' => 'review', 'payload' => $payload]);

                return $transaction;
            }

            $paidAt = data_get($data, 'transaction.date') ? Carbon::parse(data_get($data, 'transaction.date'))->setTimezone(config('app.timezone')) : now();
            $payment = Payment::create([
                'reservation_id' => $transaction->reservation_id,
                'customer_id'    => $transaction->reservation->customer_id,
                'payment_type'   => 'Deposit / Authorization',
                'payment_method' => self::METHOD,
                'date'           => $paidAt->toDateString(),
                'amount'         => $transaction->amount,
                'reference'      => $invoice,
                // Confirmed by DOKU itself, so staff don't need to approve it
                'payment_status' => 'Approved',
                'approved_at'    => now(),
                'created_by'     => $transaction->payload['user_id'] ?? null,
            ]);

            $transaction->update(['status' => 'paid', 'payment_id' => $payment->id, 'paid_at' => $paidAt, 'payload' => $payload]);

            return $transaction;
        });
    }

    /** Ask DOKU directly (customer back from the payment page before the notification arrived). */
    public function refresh(PaymentTransaction $transaction): PaymentTransaction
    {
        if ($transaction->status !== 'pending') {
            return $transaction;
        }

        try {
            return $this->apply($this->doku->status($transaction->reference), 'status') ?? $transaction;
        } catch (\Throwable $e) {
            Log::warning('DOKU: status check failed', ['invoice' => $transaction->reference, 'error' => $e->getMessage()]);

            return $transaction;
        }
    }

    /** Whole ringgit as a whole number, otherwise with its sen. */
    private static function dokuAmount(float $amount): int|float
    {
        return fmod($amount, 1) == 0 ? (int) $amount : round($amount, 2);
    }

    /** DOKU's expired_date is "yyyyMMddHHmmss" in UTC+7; a minute of margin. */
    private function stillOpen(PaymentTransaction $transaction): bool
    {
        $expires = $transaction->payload['expired_date'] ?? null;
        if (empty($transaction->payload['payment_url']) || !$expires) {
            return false;
        }

        try {
            return Carbon::createFromFormat('YmdHis', $expires, 'Asia/Jakarta')->subMinute()->isFuture();
        } catch (\Throwable) {
            return false;
        }
    }
}
