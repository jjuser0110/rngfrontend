<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\Reservation;
use App\Payments\BookingPayment;
use App\Payments\Doku\DokuClient;
use App\Payments\Doku\DokuSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/** Website bookings paid online with DOKU: the Pay button, the result page, and DOKU's payment notification. */
class PaymentController extends Controller
{
    public function __construct(private BookingPayment $payments)
    {
    }

    /** "Pay": off to DOKU's payment page for what is due on this booking. */
    public function pay(Reservation $reservation)
    {
        abort_unless(BookingPayment::enabled(), 404);
        $this->ownBooking($reservation);

        if (!BookingPayment::canPay($reservation)) {
            return $this->backWithError($reservation, 'There is nothing to pay for this booking right now.');
        }

        try {
            return redirect()->away($this->payments->start($reservation, auth()->user()));
        } catch (\Throwable $e) {
            report($e);

            return $this->backWithError($reservation, "Online payment isn't available right now. Please try again in a few minutes or contact us.");
        }
    }

    /** Where DOKU sends the customer back. Asks DOKU directly if its notification hasn't arrived yet. */
    public function result(Reservation $reservation, PaymentTransaction $transaction)
    {
        abort_unless(BookingPayment::enabled(), 404);
        $this->ownBooking($reservation);
        abort_unless($transaction->reservation_id === $reservation->id && $transaction->gateway === BookingPayment::GATEWAY, 404);

        $transaction = $this->payments->refresh($transaction);

        return view('payments.result', [
            'reservation' => $reservation->fresh(),
            'transaction' => $transaction,
        ]);
    }

    /**
     * DOKU's payment notification (server to server, no login or CSRF token).
     * Only accepted with DOKU's signature, made with our Secret Key over this exact request.
     */
    public function notify(Request $request)
    {
        abort_unless(BookingPayment::enabled(), 404);

        $doku = DokuClient::fromConfig();
        $body = $request->getContent();
        $expected = DokuSignature::make(
            (string) $request->header('Client-Id'),
            (string) $request->header('Request-Id'),
            (string) $request->header('Request-Timestamp'),
            parse_url($request->getRequestUri(), PHP_URL_PATH), // our notification path, as DOKU signed it
            $body,
            $doku->secretKey(),
        );

        if ($request->header('Client-Id') !== $doku->clientId() || !DokuSignature::matches($expected, $request->header('Signature'))) {
            Log::warning('DOKU: notification refused (bad signature)', ['ip' => $request->ip(), 'request_id' => $request->header('Request-Id')]);

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            return response()->json(['message' => 'Invalid body'], 400);
        }

        $this->payments->apply($data, 'notification');

        // 2xx tells DOKU not to send it again (unknown invoices too: there is nothing to retry)
        return response()->json(['message' => 'OK']);
    }

    /** Shown in that booking's payment box (payments.payment-box). */
    private function backWithError(Reservation $reservation, string $message)
    {
        return back()->with('payment_error', $message)->with('payment_error_for', $reservation->id);
    }

    private function ownBooking(Reservation $reservation): void
    {
        abort_unless($reservation->customer_id && $reservation->customer_id === auth()->user()->customer_id, 404);
    }
}
