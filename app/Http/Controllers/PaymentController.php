<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\Reservation;
use App\Payments\DepositPayment;
use App\Payments\Doku\DokuSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/** The security deposit paid online with DOKU: pay button, result page, and DOKU's payment notification. */
class PaymentController extends Controller
{
    public function __construct(private DepositPayment $deposits)
    {
    }

    /** "Pay deposit": off to DOKU's payment page. */
    public function pay(Reservation $reservation)
    {
        abort_unless(DepositPayment::enabled(), 404);
        $this->ownBooking($reservation);

        if (!DepositPayment::canPay($reservation)) {
            return $this->backWithError($reservation, 'There is no deposit to pay for this booking right now.');
        }

        try {
            return redirect()->away($this->deposits->start($reservation, auth()->user()));
        } catch (\Throwable $e) {
            report($e);

            return $this->backWithError($reservation, "Online payment isn't available right now. Please try again later or contact us.");
        }
    }

    /** Where DOKU sends the customer back. Asks DOKU directly if its notification hasn't arrived yet. */
    public function result(Reservation $reservation, PaymentTransaction $transaction)
    {
        abort_unless(DepositPayment::enabled(), 404);
        $this->ownBooking($reservation);
        abort_unless($transaction->reservation_id === $reservation->id && $transaction->gateway === DepositPayment::GATEWAY, 404);

        return view('payments.result', [
            'reservation' => $reservation,
            'transaction' => $this->deposits->refresh($transaction),
        ]);
    }

    /**
     * DOKU's payment notification (server to server, no login or CSRF token).
     * Only accepted with DOKU's signature, made with our Secret Key over this exact request.
     */
    public function notify(Request $request)
    {
        abort_unless(DepositPayment::enabled(), 404);

        $config = config('services.doku');
        $body = $request->getContent();
        $expected = DokuSignature::make(
            (string) $request->header('Client-Id'),
            (string) $request->header('Request-Id'),
            (string) $request->header('Request-Timestamp'),
            parse_url($request->getRequestUri(), PHP_URL_PATH), // our notification path, as DOKU signed it
            $body,
            $config['secret_key'],
        );

        if ($request->header('Client-Id') !== $config['client_id'] || !DokuSignature::matches($expected, $request->header('Signature'))) {
            Log::warning('DOKU: notification refused (bad signature)', ['ip' => $request->ip(), 'request_id' => $request->header('Request-Id')]);

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            return response()->json(['message' => 'Invalid body'], 400);
        }

        $this->deposits->apply($data, 'notification');

        // 2xx tells DOKU not to send it again (unknown invoices too: there is nothing to retry)
        return response()->json(['message' => 'OK']);
    }

    /** Shown in that booking's deposit box (payments.deposit-box). */
    private function backWithError(Reservation $reservation, string $message)
    {
        return back()->with('payment_error', $message)->with('payment_error_for', $reservation->id);
    }

    private function ownBooking(Reservation $reservation): void
    {
        abort_unless($reservation->customer_id && $reservation->customer_id === auth()->user()->customer_id, 404);
    }
}
