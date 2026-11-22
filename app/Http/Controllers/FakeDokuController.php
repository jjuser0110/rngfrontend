<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Payments\BookingPayment;
use App\Payments\Doku\DokuClient;
use App\Payments\Doku\DokuSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * LOCAL TESTING ONLY (DOKU_FAKE=true with APP_ENV=local): stands in for DOKU's payment page.
 *
 * "Pay" sends this website the same signed notification DOKU would, through PaymentController::notify, so the
 * signature check, the payments recorded for staff and the Quote all run for real. Only DOKU itself is skipped.
 */
class FakeDokuController extends Controller
{
    public function show(string $invoice)
    {
        $transaction = $this->transaction($invoice);

        return view('payments.fake-doku', [
            'transaction' => $transaction,
            'reservation' => $transaction->reservation,
        ]);
    }

    public function act(Request $request, string $invoice)
    {
        $transaction = $this->transaction($invoice);
        $resultUrl = route('payments.doku.result', [$transaction->reservation_id, $transaction]);

        switch ($request->validate(['action' => 'required|in:pay,pay_delayed,fail,expire,cancel'])['action']) {
            case 'pay':
                // As on a server: DOKU's message arrives, then the customer comes back
                $this->notify($transaction, 'SUCCESS');

                return redirect($resultUrl);

            case 'pay_delayed':
                // As on localhost: no message arrives; the result page asks DOKU, which says SUCCESS
                $this->remember($transaction, 'SUCCESS');

                return redirect($resultUrl);

            case 'fail':
                // A declined card: the customer can try again on the same page
                $this->notify($transaction, 'FAILED');

                return back()->with('fake_message', 'Card declined (fake). Try again, or choose another result.');

            case 'expire':
                // The payment page ran out of time
                $this->remember($transaction, 'EXPIRED', ['expired_date' => now('Asia/Jakarta')->subMinute()->format('YmdHis')]);
                $this->notify($transaction, 'EXPIRED');

                return redirect($resultUrl);

            default: // cancel: back to the website without paying
                return redirect($resultUrl);
        }
    }

    private function transaction(string $invoice): PaymentTransaction
    {
        abort_unless(DokuClient::fromConfig()->isFake(), 404);

        return PaymentTransaction::where('gateway', BookingPayment::GATEWAY)->where('reference', $invoice)->firstOrFail();
    }

    /** What the fake DOKU answers to a status check from now on. */
    private function remember(PaymentTransaction $transaction, string $status, array $extra = []): void
    {
        $transaction->update(['payload' => ['fake_status' => $status] + $extra + $transaction->payload]);
    }

    /** DOKU's notification, signed with the configured (or fake) keys, handled by the real notification code. */
    private function notify(PaymentTransaction $transaction, string $status): void
    {
        $this->remember($transaction, $status);

        $doku = DokuClient::fromConfig();
        $body = json_encode([
            'order'       => ['invoice_number' => $transaction->reference, 'amount' => (float) $transaction->amount],
            'transaction' => ['status' => $status, 'date' => now('UTC')->format('Y-m-d\TH:i:s\Z'), 'original_request_id' => (string) Str::uuid()],
            'service'     => ['id' => 'FAKE_DOKU'],
            'channel'     => ['id' => 'FAKE_CARD'],
        ]);
        $path = parse_url(route('payments.doku.notify'), PHP_URL_PATH);
        $requestId = (string) Str::uuid();
        $timestamp = now('UTC')->format('Y-m-d\TH:i:s\Z');

        $notification = Request::create($path, 'POST', [], [], [], [
            'CONTENT_TYPE'           => 'application/json',
            'HTTP_CLIENT_ID'         => $doku->clientId(),
            'HTTP_REQUEST_ID'        => $requestId,
            'HTTP_REQUEST_TIMESTAMP' => $timestamp,
            'HTTP_SIGNATURE'         => DokuSignature::make($doku->clientId(), $requestId, $timestamp, $path, $body, $doku->secretKey()),
        ], $body);

        $response = app(PaymentController::class)->notify($notification);
        abort_unless($response->getStatusCode() === 200, 500, 'The fake DOKU notification was refused: ' . $response->getContent());
    }
}
