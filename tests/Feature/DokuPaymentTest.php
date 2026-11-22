<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\Reservation;
use App\Models\User;
use App\Payments\Doku\DokuSignature;
use Bouncer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Security deposit paid online with DOKU Checkout. DOKU itself is faked (Http::fake) with made-up keys.
 *
 * Runs against the database shared with rngcarrental: every test is rolled back (DatabaseTransactions).
 * Never switch this to RefreshDatabase.
 */
class DokuPaymentTest extends TestCase
{
    use DatabaseTransactions;

    private const CLIENT_ID = 'TEST-CLIENT-1';
    private const SECRET = 'test-secret-key';
    private const PAGE = 'https://sandbox.doku.com/checkout-link-v2/test-token';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.doku' => [
            'enabled' => true, 'client_id' => self::CLIENT_ID, 'secret_key' => self::SECRET,
            'base_url' => 'https://api-sandbox.doku.com', 'currency' => 'MYR', 'due_minutes' => 60,
            'methods' => [], 'pay_statuses' => ['Quote', 'Open'],
        ]]);
        Http::preventStrayRequests();
    }

    private function customerUser(array $customerExtra = []): User
    {
        $username = 'zztest' . uniqid();
        $customer = Customer::create([
            'first_name' => 'Pay', 'last_name' => 'Test', 'username' => $username, 'email' => "$username@example.com",
            'password' => Hash::make('Secret#12345'), 'phone_number' => '+60123456789',
        ]);
        $customer->forceFill($customerExtra)->save();

        $user = new User();
        $user->forceFill([
            'name' => 'Pay Test', 'username' => $username, 'email' => $customer->email, 'password' => $customer->password,
            'customer_id' => $customer->id, 'role_id' => Bouncer::role()->where('name', 'customer')->value('id'), 'is_active' => 1,
        ])->save();

        return $user;
    }

    private function booking(User $user, array $extra = []): Reservation
    {
        return Reservation::create($extra + [
            'customer_id' => $user->customer_id, 'company_id' => 1, 'status' => 'Quote', 'source' => 'frontend',
            'pickup_date' => now()->addDays(5)->toDateString(), 'pickup_time' => '10:00',
            'return_date' => now()->addDays(8)->toDateString(), 'return_time' => '10:00',
            'total_price' => 300, 'security_deposit' => 200, 'created_by' => $user->id,
        ]);
    }

    private function fakeCheckout(): void
    {
        Http::fake(['api-sandbox.doku.com/checkout/v1/payment' => Http::response([
            'message' => ['SUCCESS'],
            'response' => ['payment' => [
                'url' => self::PAGE, 'token_id' => 'test-token', 'payment_due_date' => 60,
                'expired_date' => now('Asia/Jakarta')->addHour()->format('YmdHis'),
            ]],
        ])]);
    }

    /** DOKU's notification, signed with our secret key like DOKU does. */
    private function notify(array $data, ?string $secret = null, string $clientId = self::CLIENT_ID)
    {
        $body = json_encode($data);
        $requestId = 'doku-' . uniqid();
        $timestamp = now('UTC')->format('Y-m-d\TH:i:s\Z');

        return $this->call('POST', '/payments/doku/notify', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_CLIENT_ID' => $clientId,
            'HTTP_REQUEST_ID' => $requestId,
            'HTTP_REQUEST_TIMESTAMP' => $timestamp,
            'HTTP_SIGNATURE' => DokuSignature::make($clientId, $requestId, $timestamp, '/payments/doku/notify', $body, $secret ?? self::SECRET),
        ], $body);
    }

    private function success(PaymentTransaction $transaction, array $override = []): array
    {
        return array_replace_recursive([
            'order' => ['invoice_number' => $transaction->reference, 'amount' => (float) $transaction->amount],
            'transaction' => ['status' => 'SUCCESS', 'date' => now('UTC')->format('Y-m-d\TH:i:s\Z'), 'original_request_id' => 'x'],
            'service' => ['id' => 'CREDIT_CARD'], 'channel' => ['id' => 'CREDIT_CARD'],
        ], $override);
    }

    /** Customer presses "Pay deposit"; returns the pending transaction. */
    private function startPayment(User $user, Reservation $booking): PaymentTransaction
    {
        $this->fakeCheckout();
        $this->actingAs($user)->post(route('payments.doku.pay', $booking), ['_token' => csrf_token()])->assertRedirect(self::PAGE);

        return PaymentTransaction::where('reservation_id', $booking->id)->latest('id')->firstOrFail();
    }

    // ---------------------------------------------------------------- signature

    public function test_signature_follows_dokus_format(): void
    {
        $body = '{"order":{"amount":200}}';
        $lines = "Client-Id:C1\nRequest-Id:R1\nRequest-Timestamp:2026-11-22T01:00:00Z\nRequest-Target:/checkout/v1/payment\nDigest:"
            . base64_encode(hash('sha256', $body, true));
        $this->assertSame(
            'HMACSHA256=' . base64_encode(hash_hmac('sha256', $lines, 'S1', true)),
            DokuSignature::make('C1', 'R1', '2026-11-22T01:00:00Z', '/checkout/v1/payment', $body, 'S1')
        );

        // GET (status check): no Digest line
        $this->assertSame(
            'HMACSHA256=' . base64_encode(hash_hmac('sha256', "Client-Id:C1\nRequest-Id:R1\nRequest-Timestamp:T\nRequest-Target:/orders/v1/status/X", 'S1', true)),
            DokuSignature::make('C1', 'R1', 'T', '/orders/v1/status/X', null, 'S1')
        );
    }

    // ---------------------------------------------------------------- paying

    public function test_customer_is_sent_to_the_doku_payment_page(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user);

        $this->actingAs($user)->get(route('myaccount.index'))->assertSee('Pay deposit RM 200.00');
        $transaction = $this->startPayment($user, $booking);

        $this->assertSame('pending', $transaction->status);
        $this->assertSame("DEP-{$booking->id}-{$transaction->id}", $transaction->reference);
        $this->assertEquals(200, (float) $transaction->amount);

        Http::assertSent(function (HttpRequest $request) use ($transaction, $booking) {
            $body = $request->body();
            $data = json_decode($body, true);
            $expected = DokuSignature::make(self::CLIENT_ID, $request->header('Request-Id')[0], $request->header('Request-Timestamp')[0], '/checkout/v1/payment', $body, self::SECRET);

            return $request->url() === 'https://api-sandbox.doku.com/checkout/v1/payment'
                && $request->header('Client-Id')[0] === self::CLIENT_ID
                && $request->header('Signature')[0] === $expected
                && preg_match('/^\d{4}-\d\d-\d\dT\d\d:\d\d:\d\dZ$/', $request->header('Request-Timestamp')[0])
                && $data['order']['amount'] === 200
                && $data['order']['currency'] === 'MYR'
                && $data['order']['invoice_number'] === $transaction->reference
                && $data['order']['callback_url_result'] === route('payments.doku.result', [$booking, $transaction])
                && $data['additional_info']['override_notification_url'] === route('payments.doku.notify')
                && $data['payment']['payment_due_date'] === 60;
        });
    }

    public function test_pressing_pay_again_reuses_the_open_payment_page(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user);
        $this->fakeCheckout();
        $this->actingAs($user)->post(route('payments.doku.pay', $booking), ['_token' => csrf_token()])->assertRedirect(self::PAGE);
        $this->actingAs($user)->post(route('payments.doku.pay', $booking), ['_token' => csrf_token()])->assertRedirect(self::PAGE);

        $this->assertSame(1, PaymentTransaction::where('reservation_id', $booking->id)->count());
        Http::assertSentCount(1);
    }

    public function test_sen_are_sent_as_they_are(): void
    {
        $user = $this->customerUser();
        $this->startPayment($user, $this->booking($user, ['security_deposit' => 150.50]));

        Http::assertSent(fn (HttpRequest $request) => json_decode($request->body(), true)['order']['amount'] === 150.5);
    }

    public function test_doku_error_is_shown_on_the_booking_and_nothing_is_charged(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user);
        Http::fake(['api-sandbox.doku.com/*' => Http::response(['error_messages' => ['order.currency is invalid']], 400)]);

        $this->actingAs($user)->from(route('myaccount.index'))->post(route('payments.doku.pay', $booking), ['_token' => csrf_token()])
            ->assertRedirect(route('myaccount.index'));
        $this->get(route('myaccount.index'))->assertSee("Online payment isn't available right now");
        $this->assertSame('error', PaymentTransaction::where('reservation_id', $booking->id)->value('status'));
        $this->assertSame(0, Payment::where('reservation_id', $booking->id)->count());
    }

    // ---------------------------------------------------------------- DOKU's notification

    public function test_successful_payment_is_recorded_once_for_staff(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user);
        $transaction = $this->startPayment($user, $booking);

        $this->notify($this->success($transaction))->assertOk();
        $this->notify($this->success($transaction))->assertOk(); // DOKU sends it again

        $payments = Payment::where('reservation_id', $booking->id)->get();
        $this->assertCount(1, $payments, 'recorded once');
        $payment = $payments->first();
        $this->assertSame('Deposit / Authorization', $payment->payment_type);
        $this->assertSame('Online Payment (DOKU)', $payment->payment_method);
        $this->assertSame('Approved', $payment->payment_status);
        $this->assertSame($transaction->reference, $payment->reference);
        $this->assertEquals(200, (float) $payment->amount);
        $this->assertSame($user->id, (int) $payment->created_by);
        $this->assertNotEmpty($payment->receipt_no);

        $transaction->refresh();
        $this->assertSame('paid', $transaction->status);
        $this->assertSame($payment->id, (int) $transaction->payment_id);

        $this->actingAs($user)->get(route('myaccount.index'))->assertSee('Security deposit paid (RM 200.00)')->assertDontSee('Pay deposit');
        $this->actingAs($user)->post(route('payments.doku.pay', $booking), ['_token' => csrf_token()]);
        Http::assertSentCount(1); // no second payment page
    }

    public function test_notification_with_a_wrong_signature_is_refused(): void
    {
        $user = $this->customerUser();
        $transaction = $this->startPayment($user, $this->booking($user));

        $this->notify($this->success($transaction), 'someone-elses-key')->assertStatus(401);
        $this->notify($this->success($transaction), null, 'OTHER-CLIENT')->assertStatus(401);

        // Body changed after signing
        $body = json_encode($this->success($transaction));
        $this->call('POST', '/payments/doku/notify', [], [], [], [
            'CONTENT_TYPE' => 'application/json', 'HTTP_CLIENT_ID' => self::CLIENT_ID, 'HTTP_REQUEST_ID' => 'r', 'HTTP_REQUEST_TIMESTAMP' => 't',
            'HTTP_SIGNATURE' => DokuSignature::make(self::CLIENT_ID, 'r', 't', '/payments/doku/notify', $body, self::SECRET),
        ], str_replace('SUCCESS', 'SUCCESS ', $body))->assertStatus(401);

        $this->assertSame('pending', $transaction->fresh()->status);
        $this->assertSame(0, Payment::where('reservation_id', $transaction->reservation_id)->count());
    }

    public function test_wrong_amount_is_held_for_staff_not_recorded(): void
    {
        $user = $this->customerUser();
        $transaction = $this->startPayment($user, $this->booking($user));

        $this->notify($this->success($transaction, ['order' => ['amount' => 1]]))->assertOk();

        $this->assertSame('review', $transaction->fresh()->status);
        $this->assertSame(0, Payment::where('reservation_id', $transaction->reservation_id)->count());
    }

    public function test_failed_card_waits_and_expired_closes(): void
    {
        $user = $this->customerUser();
        $transaction = $this->startPayment($user, $this->booking($user));

        $this->notify($this->success($transaction, ['transaction' => ['status' => 'FAILED']]))->assertOk();
        $this->assertSame('pending', $transaction->fresh()->status, 'the customer can still pay another way on the same page');

        $this->notify($this->success($transaction, ['transaction' => ['status' => 'EXPIRED']]))->assertOk();
        $this->assertSame('expired', $transaction->fresh()->status);
        $this->assertSame(0, Payment::where('reservation_id', $transaction->reservation_id)->count());
    }

    public function test_unknown_invoice_is_acknowledged_and_ignored(): void
    {
        $this->notify(['order' => ['invoice_number' => 'DEP-0-0', 'amount' => 200], 'transaction' => ['status' => 'SUCCESS']])->assertOk();
    }

    // ---------------------------------------------------------------- back from DOKU

    public function test_back_before_the_notification_asks_doku_directly(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user);
        $transaction = $this->startPayment($user, $booking);

        Http::fake(['api-sandbox.doku.com/orders/v1/status/*' => Http::response($this->success($transaction))]);
        $this->actingAs($user)->get(route('payments.doku.result', [$booking, $transaction]))->assertOk()->assertSee('Deposit paid');

        $this->assertSame('paid', $transaction->fresh()->status);
        Http::assertSent(fn (HttpRequest $request) => $request->method() === 'GET'
            && $request->url() === "https://api-sandbox.doku.com/orders/v1/status/{$transaction->reference}"
            && $request->header('Signature')[0] === DokuSignature::make(self::CLIENT_ID, $request->header('Request-Id')[0], $request->header('Request-Timestamp')[0], "/orders/v1/status/{$transaction->reference}", null, self::SECRET));
    }

    public function test_still_waiting_page(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user);
        $transaction = $this->startPayment($user, $booking);

        Http::fake(['api-sandbox.doku.com/orders/v1/status/*' => Http::response(['transaction' => ['status' => 'PENDING'], 'order' => ['invoice_number' => $transaction->reference]])]);
        $this->actingAs($user)->get(route('payments.doku.result', [$booking, $transaction]))->assertOk()
            ->assertSee('Waiting for payment confirmation')->assertSee('Refresh');
    }

    // ---------------------------------------------------------------- who and when

    public function test_only_the_customer_can_pay_or_see_their_payment(): void
    {
        $owner = $this->customerUser();
        $booking = $this->booking($owner);
        $transaction = $this->startPayment($owner, $booking);
        $other = $this->customerUser();

        $this->actingAs($other)->post(route('payments.doku.pay', $booking), ['_token' => csrf_token()])->assertNotFound();
        $this->actingAs($other)->get(route('payments.doku.result', [$booking, $transaction]))->assertNotFound();
    }

    public function test_no_button_before_the_quotation_or_without_a_deposit(): void
    {
        $user = $this->customerUser();
        $pending = $this->booking($user, ['status' => 'Pending']);
        $this->booking($user, ['status' => 'Quote', 'security_deposit' => 0]);

        $this->actingAs($user)->get(route('myaccount.index'))->assertDontSee('Pay deposit');
        $this->actingAs($user)->post(route('payments.doku.pay', $pending), ['_token' => csrf_token()])->assertRedirect();
        $this->assertSame(0, PaymentTransaction::where('reservation_id', $pending->id)->count());
    }

    public function test_switched_off_until_doku_is_set_up(): void
    {
        config(['services.doku.enabled' => false]);
        $user = $this->customerUser();
        $booking = $this->booking($user);

        $this->actingAs($user)->get(route('myaccount.index'))->assertDontSee('Pay deposit');
        $this->actingAs($user)->post(route('payments.doku.pay', $booking), ['_token' => csrf_token()])->assertNotFound();
        $this->post('/payments/doku/notify')->assertNotFound();
    }
}
