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
 * Website bookings paid online with DOKU (SenangPay).
 *
 *  - "Pay & book": the customer pays the rental (with insurance) and the security deposit. The booking waits as
 *    "Awaiting Payment" (hidden from staff) and becomes a Quote once DOKU confirms the payment. An unpaid one
 *    simply stays unpaid; the customer can pay it later from My Account, until its pick-up time.
 *    The customer chooses to pay the full amount, or the deposit only (the rental is paid after staff confirm).
 *    A customer with no deposit who chooses "Book now, pay later" pays nothing now: the booking goes straight to
 *    a Quote, marked by a payment_transactions row with status "pay_later" (gateway "website", amount 0).
 *  - "Pay balance" (only with DOKU_PAY_BALANCE_ONLINE): once staff confirm (status Open / Rental), whatever is still
 *    due can be paid the same way. Off (the default): staff collect it by hand; the customer only sees the balance.
 *
 * Each attempt is a payment_transactions row (gateway "doku", reference = the DOKU invoice number). Only DOKU's
 * signed notification, or DOKU's own status answer, marks it paid. That records it once as Payments staff see in
 * rngcarrental: the rental part as "Payment" and the deposit part as "Deposit / Authorization", both Approved.
 */
class BookingPayment
{
    public const GATEWAY = 'doku';
    public const METHOD = 'Online Payment (DOKU)';

    /** "Book now, pay later" marker (no payment): shows staff the Quote is a website booking to confirm. */
    public const PAY_LATER_GATEWAY = 'website';
    public const PAY_LATER = 'pay_later';

    public function __construct(private DokuClient $doku)
    {
    }

    public static function enabled(): bool
    {
        return DokuClient::fromConfig()->isConfigured();
    }

    /**
     * What is still to pay: ['payment' => rental and charges, 'deposit' => security deposit, 'total' => both].
     * Denied payments don't count.
     */
    public static function due(Reservation $reservation): array
    {
        $paid = fn (string $type) => (float) Payment::where('reservation_id', $reservation->id)
            ->where('payment_type', $type)
            ->where(fn ($q) => $q->whereNull('payment_status')->orWhere('payment_status', '!=', 'Denied'))
            ->sum('amount');

        $payment = max(0, round((float) $reservation->total_price - $paid('Payment'), 2));
        $deposit = max(0, round((float) $reservation->security_deposit - $paid('Deposit / Authorization'), 2));

        return ['payment' => $payment, 'deposit' => $deposit, 'total' => round($payment + $deposit, 2)];
    }

    /** A "Pay" button shows: a new booking waiting for its payment, or a balance on an active booking. */
    public static function canPay(Reservation $reservation): bool
    {
        if (!self::enabled() || !$reservation->isFrontend()) {
            return false;
        }

        $payable = $reservation->status === Reservation::STATUS_AWAITING_PAYMENT
            ? !self::pickupPassed($reservation) // no paying for dates already gone
            : self::balanceOnline() && in_array($reservation->status, config('services.doku.pay_statuses', []), true);

        return $payable && self::due($reservation)['total'] > 0;
    }

    /** What is left after booking (rest of the rental, extra charges) may be paid online (DOKU_PAY_BALANCE_ONLINE). */
    public static function balanceOnline(): bool
    {
        return (bool) config('services.doku.pay_balance_online', false);
    }

    /** A new booking with both a rental and a deposit to pay may be paid with the deposit only (the rest after confirm). */
    public static function canPayDepositOnly(Reservation $reservation): bool
    {
        $due = self::due($reservation);

        return self::depositOnlyAllowed()
            && $reservation->status === Reservation::STATUS_AWAITING_PAYMENT
            && $due['payment'] > 0 && $due['deposit'] > 0;
    }

    /** The "Pay deposit only" / "Book now, pay later" choice on the details page (DOKU_ALLOW_DEPOSIT_ONLY). */
    public static function depositOnlyAllowed(): bool
    {
        return self::enabled() && (bool) config('services.doku.allow_deposit_only', true);
    }

    /** "Book now, pay later": a Quote for staff to confirm, nothing paid. */
    public static function bookPayLater(Reservation $reservation, User $user): void
    {
        PaymentTransaction::create([
            'reservation_id' => $reservation->id,
            'gateway'        => self::PAY_LATER_GATEWAY,
            'amount'         => 0,
            'currency'       => config('services.doku.currency'),
            'status'         => self::PAY_LATER,
            'payload'        => ['user_id' => $user->id],
        ]);
        $reservation->update(['status' => 'Quote']);
    }

    /**
     * How a website Quote waiting for staff was booked: 'paid' (full amount), 'deposit' (deposit only),
     * 'pay_later' (nothing paid), or null (not one of those).
     */
    public static function websiteQuoteKind(Reservation $reservation): ?string
    {
        if ($reservation->status !== 'Quote' || !$reservation->isFrontend()) {
            return null;
        }

        $statuses = $reservation->paymentTransactions()->whereIn('status', ['paid', self::PAY_LATER])->pluck('status');
        if ($statuses->contains('paid')) {
            // Deposit only: no rental payment at all yet (a full payment stays "paid" even if staff raise the price)
            $rentalPaid = Payment::where('reservation_id', $reservation->id)->where('payment_type', 'Payment')
                ->where(fn ($q) => $q->whereNull('payment_status')->orWhere('payment_status', '!=', 'Denied'))->exists();

            return $rentalPaid ? 'paid' : 'deposit';
        }

        return $statuses->contains(self::PAY_LATER) ? self::PAY_LATER : null;
    }

    public static function pickupPassed(Reservation $reservation): bool
    {
        return Carbon::parse($reservation->pickup_date . ' ' . $reservation->pickup_time)->isPast();
    }

    /**
     * DOKU payment page for what is due: an open one for the same amount is reused, else a new one is made.
     * $depositOnly: a new booking paid with the deposit only (when allowed); the rental is paid after staff confirm.
     */
    public function start(Reservation $reservation, User $user, bool $depositOnly = false): string
    {
        $due = self::due($reservation);
        if ($depositOnly && self::canPayDepositOnly($reservation)) {
            $due = ['payment' => 0, 'deposit' => $due['deposit'], 'total' => $due['deposit']];
        }

        $open = $reservation->paymentTransactions()
            ->where('gateway', self::GATEWAY)->where('status', 'pending')->latest('id')->get()
            ->first(fn ($transaction) => abs((float) $transaction->amount - $due['total']) < 0.01 && $this->stillOpen($transaction));
        if ($open) {
            return $open->payload['payment_url'];
        }

        $transaction = PaymentTransaction::create([
            'reservation_id' => $reservation->id,
            'gateway'        => self::GATEWAY,
            'amount'         => $due['total'],
            'currency'       => config('services.doku.currency'),
            'status'         => 'pending',
            // How the amount is split when it is recorded, fixed now so a later price change can't alter it
            'payload'        => ['user_id' => $user->id, 'parts' => ['payment' => $due['payment'], 'deposit' => $due['deposit']]],
        ]);
        // Letters, digits and dashes only, under 30 characters (DOKU's limit with card payments)
        $transaction->update(['reference' => 'RNG-' . $reservation->id . '-' . $transaction->id]);

        $code = $reservation->code ?? $reservation->id;
        $lines = array_values(array_filter([
            $due['payment'] > 0 ? ['id' => 'rental-' . $reservation->id, 'name' => "Car rental, booking $code", 'price' => self::dokuAmount($due['payment']), 'quantity' => 1] : null,
            $due['deposit'] > 0 ? ['id' => 'deposit-' . $reservation->id, 'name' => "Refundable security deposit, booking $code", 'price' => self::dokuAmount($due['deposit']), 'quantity' => 1] : null,
        ]));

        $resultUrl = route('payments.doku.result', [$reservation, $transaction]);
        try {
            $response = $this->doku->createCheckout(
                [
                    'amount'              => self::dokuAmount($due['total']),
                    'invoice_number'      => $transaction->reference,
                    'callback_url'        => $resultUrl,
                    'callback_url_result' => $resultUrl,
                    'line_items'          => $lines,
                ],
                // Required by DOKU (Malaysia): name, email and phone
                array_filter([
                    'id'    => (string) $reservation->customer_id,
                    'name'  => mb_substr((string) ($reservation->customer->full_name ?? ''), 0, 255),
                    'email' => $reservation->customer->email ?? null,
                    'phone' => self::dokuPhone($reservation->customer->phone_number ?? null),
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
     * SUCCESS with the right amount records the payment once and turns a new booking into a Quote;
     * EXPIRED closes the attempt; anything else waits (a FAILED card can still be paid another way on the same page).
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
                Log::error('DOKU: paid amount differs from the amount asked', ['invoice' => $invoice, 'paid' => data_get($data, 'order.amount'), 'asked' => $transaction->amount]);
                $transaction->update(['status' => 'review', 'payload' => $payload]);

                return $transaction;
            }

            $paidAt = data_get($data, 'transaction.date') ? Carbon::parse(data_get($data, 'transaction.date'))->setTimezone(config('app.timezone')) : now();
            $parts = $transaction->payload['parts'] ?? ['payment' => 0, 'deposit' => (float) $transaction->amount];
            $reservation = Reservation::withTrashed()->whereKey($transaction->reservation_id)->lockForUpdate()->first();
            // The customer removed the unpaid booking but then paid its open DOKU page: the money is real, so keep it
            if ($reservation->trashed()) {
                Log::warning('DOKU: payment for a booking the customer had removed; booking restored', ['reservation' => $reservation->id, 'invoice' => $invoice]);
                $reservation->restore();
            }

            $paymentIds = [];
            foreach (['Payment' => $parts['payment'] ?? 0, 'Deposit / Authorization' => $parts['deposit'] ?? 0] as $type => $amount) {
                if ($amount <= 0) {
                    continue;
                }
                $paymentIds[] = Payment::create([
                    'reservation_id' => $reservation->id,
                    'customer_id'    => $reservation->customer_id,
                    'payment_type'   => $type,
                    'payment_method' => self::METHOD,
                    'date'           => $paidAt->toDateString(),
                    'amount'         => $amount,
                    'reference'      => $invoice,
                    // Confirmed by DOKU itself; staff can still check it in the DOKU / SenangPay back office
                    'payment_status' => 'Approved',
                    'approved_at'    => now(),
                    'created_by'     => $transaction->payload['user_id'] ?? null,
                ])->id;
            }

            $transaction->update([
                'status'     => 'paid',
                'payment_id' => $paymentIds[0] ?? null,
                'paid_at'    => $paidAt,
                'payload'    => ['payment_ids' => $paymentIds] + $payload,
            ]);

            // A new booking is paid: staff now see it as a Quote to check and confirm
            if ($reservation->status === Reservation::STATUS_AWAITING_PAYMENT) {
                $reservation->update(['status' => 'Quote']);
            }

            return $transaction;
        });
    }

    /** Ask DOKU directly (customer back from the payment page before the notification arrived). */
    public function refresh(PaymentTransaction $transaction): PaymentTransaction
    {
        if ($transaction->status !== 'pending' || !$transaction->reference) {
            return $transaction;
        }

        try {
            return $this->apply($this->doku->status($transaction->reference), 'status') ?? $transaction;
        } catch (\Throwable $e) {
            Log::warning('DOKU: status check failed', ['invoice' => $transaction->reference, 'error' => $e->getMessage()]);

            return $transaction;
        }
    }

    /**
     * Phone as DOKU wants it: country code and number, digits only, max 16 (e.g. 60123456789).
     * "+60 12-345 6789" -> 60123456789; a local "012-345 6789" is Malaysian -> 60123456789.
     */
    public static function dokuPhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '0')) {
            $digits = '6' . $digits;
        }

        return substr($digits, 0, 16);
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
