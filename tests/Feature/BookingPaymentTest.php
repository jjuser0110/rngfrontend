<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Location;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleClass;
use App\Models\VehicleModel;
use App\Payments\Doku\DokuSignature;
use Bouncer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Website bookings paid online with DOKU (SenangPay): pay & book, the payment recorded for staff, unpaid bookings
 * expiring, balances, and the assigned car. DOKU itself is faked (Http::fake) with made-up keys.
 *
 * Runs against the database shared with rngcarrental: every test is rolled back (DatabaseTransactions).
 * Never switch this to RefreshDatabase.
 */
class BookingPaymentTest extends TestCase
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
            'methods' => [], 'pay_statuses' => ['Open', 'Rental'],
        ]]);
        Http::preventStrayRequests();
    }

    private function customerUser(array $customerExtra = []): User
    {
        $username = 'zztest' . uniqid();
        $customer = Customer::create([
            'first_name' => 'Pay', 'last_name' => 'Test', 'username' => $username, 'email' => "$username@example.com",
            'password' => Hash::make('Secret#12345'), 'phone_number' => '+60123456789',
            'id_type' => 'ic', 'ic' => '900101-13-5555', 'expiration_date' => now()->addYear()->toDateString(),
        ]);
        $customer->forceFill($customerExtra)->save();

        $user = new User();
        $user->forceFill([
            'name' => 'Pay Test', 'username' => $username, 'email' => $customer->email, 'password' => $customer->password,
            'customer_id' => $customer->id, 'role_id' => Bouncer::role()->where('name', 'customer')->value('id'), 'is_active' => 1,
        ])->save();

        return $user;
    }

    /** A website booking in a given state (no rate lines; total_price set directly). */
    private function booking(User $user, array $extra = []): Reservation
    {
        return Reservation::create($extra + [
            'customer_id' => $user->customer_id, 'company_id' => 1, 'status' => Reservation::STATUS_AWAITING_PAYMENT, 'source' => 'frontend',
            'pickup_date' => now()->addDays(5)->toDateString(), 'pickup_time' => '10:00',
            'return_date' => now()->addDays(8)->toDateString(), 'return_time' => '10:00',
            'total_price' => 300, 'security_deposit' => 200, 'created_by' => $user->id,
        ]);
    }

    /** Logged-in customer with a car chosen and the cart done: 3 days x RM 100 (CDR not ticked) = RM 300. */
    private function cartReady(User $user): VehicleModel
    {
        $class = VehicleClass::create(['name' => 'ZZ Class ' . uniqid(), 'is_active' => 1, 'daily_rate' => 100, 'hourly_rate' => 10, 'insurance' => 20]);
        $car = VehicleModel::create(['name' => 'ZZ Car ' . uniqid(), 'vehicle_class_id' => $class->id, 'is_active' => 1, 'show_on_frontend' => 1]);
        $branch = Location::create(['name' => 'ZZ Branch ' . uniqid(), 'is_active' => 1, 'is_airport' => 0]);

        $this->actingAs($user);
        $this->get(route('cars.select', $car));
        $this->post(route('cars.cart.update'), [
            '_token' => csrf_token(),
            'pickup_location_id' => $branch->id, 'return_location_id' => $branch->id,
            'pickup_date' => now()->addDays(7)->toDateString(), 'pickup_time' => '10:00',
            'return_date' => now()->addDays(10)->toDateString(), 'return_time' => '10:00',
        ])->assertRedirect(route('cars.fillinfo'));

        return $car;
    }

    /** What the faked DOKU answers to a status check (tests change it after a payment started). */
    private array $dokuStatus = ['transaction' => ['status' => 'PENDING']];

    private function fakeDoku(?array $status = null): void
    {
        if ($status !== null) {
            $this->dokuStatus = $status;
        }

        Http::fake([
            'api-sandbox.doku.com/checkout/v1/payment' => Http::response([
                'message' => ['SUCCESS'],
                'response' => ['payment' => [
                    'url' => self::PAGE, 'token_id' => 'test-token', 'payment_due_date' => 60,
                    'expired_date' => now('Asia/Jakarta')->addHour()->format('YmdHis'),
                ]],
            ]),
            'api-sandbox.doku.com/orders/v1/status/*' => fn () => Http::response($this->dokuStatus),
        ]);
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

    /** Customer presses Pay on an existing booking; returns the pending transaction. */
    private function startPayment(User $user, Reservation $booking): PaymentTransaction
    {
        $this->fakeDoku();
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

    // ---------------------------------------------------------------- pay & book

    public function test_details_page_asks_to_pay_rental_and_deposit(): void
    {
        $this->cartReady($this->customerUser());

        $this->get(route('cars.fillinfo'))->assertOk()
            ->assertSee('To pay now')
            ->assertSee('RM 500.00')            // rental 300 + deposit 200
            ->assertSee('Pay RM 500.00 &amp; Book', false)
            ->assertDontSee('Send Booking Request')
            ->assertDontSee('Estimated Total');
    }

    public function test_pay_and_book_sends_the_customer_to_doku_and_hides_the_booking_until_paid(): void
    {
        $user = $this->customerUser();
        $this->cartReady($user);
        $this->fakeDoku();

        $this->post(route('cars.submit'), ['_token' => csrf_token(), 'phone_number' => '+60123456789'])->assertRedirect(self::PAGE);

        $booking = Reservation::where('customer_id', $user->customer_id)->firstOrFail();
        $this->assertSame(Reservation::STATUS_AWAITING_PAYMENT, $booking->status);
        $this->assertNull($booking->quotation_no);
        $this->assertEquals(300, (float) $booking->total_price);

        $transaction = PaymentTransaction::where('reservation_id', $booking->id)->firstOrFail();
        $this->assertEquals(500, (float) $transaction->amount);
        $this->assertEquals(['payment' => 300, 'deposit' => 200], $transaction->payload['parts']);
        $this->assertSame("RNG-{$booking->id}-{$transaction->id}", $transaction->reference);

        Http::assertSent(function (HttpRequest $request) use ($transaction) {
            $body = $request->body();
            $data = json_decode($body, true);

            return $request->url() === 'https://api-sandbox.doku.com/checkout/v1/payment'
                && $request->header('Signature')[0] === DokuSignature::make(self::CLIENT_ID, $request->header('Request-Id')[0], $request->header('Request-Timestamp')[0], '/checkout/v1/payment', $body, self::SECRET)
                && $data['order']['amount'] === 500
                && $data['order']['currency'] === 'MYR'
                && $data['order']['invoice_number'] === $transaction->reference
                && count($data['order']['line_items']) === 2
                && $data['order']['line_items'][0]['price'] === 300
                && $data['order']['line_items'][1]['price'] === 200
                && $data['additional_info']['override_notification_url'] === route('payments.doku.notify')
                // DOKU (Malaysia) requires the customer's name, email and phone
                && $data['customer']['name'] === 'Pay Test'
                && str_ends_with($data['customer']['email'], '@example.com')
                && $data['customer']['phone'] === '60123456789';
        });
    }

    public function test_phone_is_sent_the_way_doku_wants_it(): void
    {
        $this->assertSame('60123456789', \App\Payments\BookingPayment::dokuPhone('+60 12-345 6789'));
        $this->assertSame('60123456789', \App\Payments\BookingPayment::dokuPhone('012-345 6789'));
        $this->assertSame('6591234567', \App\Payments\BookingPayment::dokuPhone('+65 9123 4567'));
        $this->assertNull(\App\Payments\BookingPayment::dokuPhone(null));
        $this->assertSame(16, strlen(\App\Payments\BookingPayment::dokuPhone('+60 12345678901234567')));
    }

    public function test_paid_booking_becomes_a_quote_with_two_payments_for_staff(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user);
        $transaction = $this->startPayment($user, $booking);

        $this->notify($this->success($transaction))->assertOk();
        $this->notify($this->success($transaction))->assertOk(); // DOKU sends it again

        $booking->refresh();
        $this->assertSame('Quote', $booking->status);
        $this->assertNotEmpty($booking->quotation_no);

        $payments = Payment::where('reservation_id', $booking->id)->orderBy('id')->get();
        $this->assertCount(2, $payments, 'recorded once: rental + deposit');
        $this->assertSame(['Payment', 'Deposit / Authorization'], $payments->pluck('payment_type')->all());
        $this->assertEquals([300, 200], $payments->pluck('amount')->map(fn ($a) => (float) $a)->all());
        foreach ($payments as $payment) {
            $this->assertSame('Online Payment (DOKU)', $payment->payment_method);
            $this->assertSame('Approved', $payment->payment_status);
            $this->assertSame($transaction->reference, $payment->reference);
            $this->assertNotEmpty($payment->receipt_no);
        }
        $this->assertSame('paid', $transaction->fresh()->status);

        $this->actingAs($user)->get(route('myaccount.index'))
            ->assertSee('Paid · awaiting confirmation')->assertSee('Fully paid')->assertDontSee('Pay RM');
        $this->get(route('cars.confirmed', $booking))->assertSee('Booking paid')->assertSee('We confirm your booking');
    }

    public function test_customer_without_deposit_pays_the_rental_only(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user, ['security_deposit' => 0]);
        $transaction = $this->startPayment($user, $booking);
        $this->assertEquals(300, (float) $transaction->amount);

        $this->notify($this->success($transaction))->assertOk();
        $this->assertSame(['Payment'], Payment::where('reservation_id', $booking->id)->pluck('payment_type')->all());
    }

    public function test_doku_error_keeps_the_booking_so_the_customer_can_pay_again(): void
    {
        $user = $this->customerUser();
        $this->cartReady($user);
        Http::fake(['api-sandbox.doku.com/*' => Http::response(['error_messages' => ['order.currency is invalid']], 400)]);

        $response = $this->post(route('cars.submit'), ['_token' => csrf_token(), 'phone_number' => '+60123456789']);
        $booking = Reservation::where('customer_id', $user->customer_id)->firstOrFail();
        $response->assertRedirect(route('cars.confirmed', $booking));

        $this->get(route('cars.confirmed', $booking))->assertOk()
            ->assertSee('Complete your payment')
            ->assertSee("Online payment isn't available right now")
            ->assertSee('Pay RM 500.00 to complete booking');
        $this->assertSame(Reservation::STATUS_AWAITING_PAYMENT, $booking->status);
    }

    // ---------------------------------------------------------------- DOKU's messages

    public function test_notification_with_a_wrong_signature_is_refused(): void
    {
        $user = $this->customerUser();
        $transaction = $this->startPayment($user, $this->booking($user));

        $this->notify($this->success($transaction), 'someone-elses-key')->assertStatus(401);
        $this->notify($this->success($transaction), null, 'OTHER-CLIENT')->assertStatus(401);

        $body = json_encode($this->success($transaction));
        $this->call('POST', '/payments/doku/notify', [], [], [], [
            'CONTENT_TYPE' => 'application/json', 'HTTP_CLIENT_ID' => self::CLIENT_ID, 'HTTP_REQUEST_ID' => 'r', 'HTTP_REQUEST_TIMESTAMP' => 't',
            'HTTP_SIGNATURE' => DokuSignature::make(self::CLIENT_ID, 'r', 't', '/payments/doku/notify', $body, self::SECRET),
        ], str_replace('SUCCESS', 'SUCCESS ', $body))->assertStatus(401); // body changed after signing

        $this->assertSame('pending', $transaction->fresh()->status);
        $this->assertSame(Reservation::STATUS_AWAITING_PAYMENT, $transaction->reservation->fresh()->status);
        $this->assertSame(0, Payment::where('reservation_id', $transaction->reservation_id)->count());
    }

    public function test_wrong_amount_is_held_for_staff_and_not_recorded(): void
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
    }

    public function test_back_before_the_notification_asks_doku_directly(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user);
        $transaction = $this->startPayment($user, $booking);

        $this->fakeDoku($this->success($transaction));
        $this->actingAs($user)->get(route('payments.doku.result', [$booking, $transaction]))->assertOk()
            ->assertSee('Payment received')->assertSee('Our team will check and confirm your booking');
        $this->assertSame('Quote', $booking->fresh()->status);
    }

    // ---------------------------------------------------------------- unpaid bookings

    public function test_unpaid_booking_stays_unpaid_and_can_be_paid_later(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user);
        $booking->forceFill(['created_at' => now()->subDays(2)])->save();
        $old = $this->startPayment($user, $booking);
        // DOKU's page for that first try has expired
        $old->update(['payload' => ['expired_date' => now('Asia/Jakarta')->subDay()->format('YmdHis')] + $old->payload]);

        $this->actingAs($user)->get(route('myaccount.index'))
            ->assertSee('Payment not completed')->assertSee('Pay RM 500.00 to complete booking');
        $this->get(route('cars.confirmed', $booking))->assertSee('Complete your payment')->assertSee('We only confirm it and keep a car for you once it is paid');

        // Pressing Pay again opens a new DOKU page, and paying it books as usual
        $this->post(route('payments.doku.pay', $booking), ['_token' => csrf_token()])->assertRedirect(self::PAGE);
        $new = PaymentTransaction::where('reservation_id', $booking->id)->latest('id')->first();
        $this->assertNotSame($old->id, $new->id);

        $this->notify($this->success($new))->assertOk();
        $this->assertSame('Quote', $booking->fresh()->status);
        $this->assertSame(2, Payment::where('reservation_id', $booking->id)->count());
    }

    public function test_unpaid_booking_cannot_be_paid_after_its_pickup_time(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user, ['pickup_date' => now()->subDay()->toDateString(), 'return_date' => now()->addDay()->toDateString()]);
        $this->fakeDoku();

        // Not listed in My Account any more; its own page (e.g. an old link) says why, with Remove
        $this->actingAs($user)->get(route('myaccount.index'))->assertDontSee($booking->code)->assertDontSee('Pay RM');
        $this->get(route('cars.confirmed', $booking))->assertSee('This booking was not paid')->assertSee('Remove')->assertDontSee('Pay RM');
        $this->post(route('payments.doku.pay', $booking), ['_token' => csrf_token()]);
        $this->assertSame(0, PaymentTransaction::where('reservation_id', $booking->id)->count());
        Http::assertNothingSent();
    }

    public function test_unpaid_bookings_with_a_future_pickup_stay_listed(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user);
        $this->actingAs($user)->get(route('myaccount.index'))->assertSee($booking->code)->assertSee('Payment not completed')->assertSee('Remove');
    }

    public function test_customer_removes_an_unpaid_booking(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user);
        $transaction = $this->startPayment($user, $booking);

        $this->actingAs($user)->delete(route('myaccount.bookings.remove', $booking), ['_token' => csrf_token()])
            ->assertRedirect(route('myaccount.index'));

        $this->assertSoftDeleted('reservations', ['id' => $booking->id]);
        $this->assertSame('cancelled', $transaction->fresh()->status);
        $this->get(route('myaccount.index'))->assertSee('Booking ' . $booking->code . ' removed.')->assertDontSee('Payment not completed');
        $this->get(route('cars.confirmed', $booking))->assertNotFound();
        $this->post(route('payments.doku.pay', $booking), ['_token' => csrf_token()])->assertNotFound();
    }

    public function test_only_unpaid_bookings_of_your_own_can_be_removed(): void
    {
        $user = $this->customerUser();
        $paid = $this->booking($user, ['status' => 'Quote']);
        $staffBooking = $this->booking($user, ['source' => 'backend']);
        $someoneElses = $this->booking($this->customerUser());

        foreach ([$paid, $staffBooking, $someoneElses] as $booking) {
            $this->actingAs($user)->delete(route('myaccount.bookings.remove', $booking), ['_token' => csrf_token()])->assertNotFound();
            $this->assertNotSoftDeleted('reservations', ['id' => $booking->id]);
        }
        $this->actingAs($user)->get(route('myaccount.index'))->assertDontSee('fa-trash-o', false);
    }

    public function test_paying_a_removed_booking_brings_it_back(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user);
        $transaction = $this->startPayment($user, $booking);
        $this->actingAs($user)->delete(route('myaccount.bookings.remove', $booking), ['_token' => csrf_token()]);

        // The DOKU page was still open and the customer paid it anyway
        $this->notify($this->success($transaction))->assertOk();

        $booking = Reservation::find($booking->id);
        $this->assertNotNull($booking, 'restored');
        $this->assertSame('Quote', $booking->status);
        $this->assertSame(2, Payment::where('reservation_id', $booking->id)->count());
    }

    // ---------------------------------------------------------------- balance and car

    public function test_balance_is_collected_by_staff_not_paid_online(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user, ['status' => 'Open', 'total_price' => 420]);
        foreach (['Payment' => 300, 'Deposit / Authorization' => 200] as $type => $amount) {
            Payment::create(['reservation_id' => $booking->id, 'customer_id' => $user->customer_id, 'payment_type' => $type,
                'payment_method' => 'Online Payment (DOKU)', 'date' => today(), 'amount' => $amount, 'payment_status' => 'Approved']);
        }

        $this->actingAs($user)->get(route('myaccount.index'))
            ->assertSee('Balance to pay: RM 120.00')
            ->assertSee('Please pay at the counter when you pick up your car, or our staff will contact you about payment.')
            ->assertDontSee('Pay balance');
        $this->post(route('payments.doku.pay', $booking), ['_token' => csrf_token()]);
        $this->assertSame(0, PaymentTransaction::where('reservation_id', $booking->id)->count(), 'no online balance payment');

        // Staff record the counter payment
        Payment::create(['reservation_id' => $booking->id, 'customer_id' => $user->customer_id, 'payment_type' => 'Payment',
            'payment_method' => 'Cash', 'date' => today(), 'amount' => 120, 'payment_status' => 'Approved']);
        $this->get(route('myaccount.index'))->assertSee('Fully paid')->assertDontSee('Balance to pay');
    }

    public function test_balance_after_staff_raise_the_price_when_paying_online_is_on(): void
    {
        config(['services.doku.pay_balance_online' => true]);
        $user = $this->customerUser();
        $booking = $this->booking($user, ['status' => 'Open']);
        foreach (['Payment' => 300, 'Deposit / Authorization' => 200] as $type => $amount) {
            Payment::create(['reservation_id' => $booking->id, 'customer_id' => $user->customer_id, 'payment_type' => $type,
                'payment_method' => 'Online Payment (DOKU)', 'date' => today(), 'amount' => $amount, 'payment_status' => 'Approved']);
        }
        $this->actingAs($user)->get(route('myaccount.index'))->assertSee('Fully paid');

        $booking->update(['total_price' => 420]); // staff added the driver
        $this->get(route('myaccount.index'))->assertSee('Pay balance RM 120.00');

        $transaction = $this->startPayment($user, $booking);
        $this->assertEquals(120, (float) $transaction->amount);
        Http::assertSent(fn (HttpRequest $request) => count(json_decode($request->body(), true)['order']['line_items']) === 1);

        $this->notify($this->success($transaction))->assertOk();
        $this->assertSame('Open', $booking->fresh()->status, 'a balance does not change the status');
        $this->assertEquals(420, (float) Payment::where('reservation_id', $booking->id)->where('payment_type', 'Payment')->sum('amount'));
        $this->get(route('myaccount.index'))->assertSee('Fully paid')->assertDontSee('Pay balance');
    }

    public function test_customer_sees_the_assigned_car_once_confirmed(): void
    {
        $user = $this->customerUser();
        $vehicle = Vehicle::whereNotNull('license_plate')->firstOrFail();
        $quote = $this->booking($user, ['status' => 'Quote', 'vehicle_id' => $vehicle->id]);

        $this->actingAs($user)->get(route('myaccount.index'))->assertDontSee($vehicle->license_plate);

        $quote->update(['status' => 'Open']);
        $this->get(route('myaccount.index'))->assertSee('Your car:')->assertSee($vehicle->license_plate);
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

    public function test_payment_off_keeps_the_booking_request_flow(): void
    {
        config(['services.doku.enabled' => false]);
        $user = $this->customerUser();
        $this->cartReady($user);

        $this->get(route('cars.fillinfo'))->assertSee('Send Booking Request')->assertSee('Estimated Total')->assertDontSee('To pay now');
        $this->post(route('cars.submit'), ['_token' => csrf_token(), 'phone_number' => '+60123456789']);
        $this->assertSame('Pending', Reservation::where('customer_id', $user->customer_id)->value('status'));

        $this->post('/payments/doku/notify')->assertNotFound();
    }

    // ---------------------------------------------------------------- full amount or deposit only

    public function test_details_page_offers_full_amount_or_deposit_only(): void
    {
        $this->cartReady($this->customerUser());

        $this->get(route('cars.fillinfo'))->assertOk()
            ->assertSee('How would you like to pay?')
            ->assertSee('Pay full amount')
            ->assertSee('Pay deposit only')
            ->assertSee('Rental RM 300.00 is paid at the counter when you pick up your car, or our staff will contact you about payment.')
            ->assertSee('Pay RM 500.00 &amp; Book', false)   // full amount pre-selected
            ->assertSee('data-button="Pay RM 200.00 &amp; Book"', false)
            ->assertDontSee('Book now, pay later');

        // The cart shows the full amount, without the choice
        $this->get(route('cars.cart'))->assertDontSee('How would you like to pay?');
    }

    public function test_deposit_only_pays_the_deposit_and_leaves_the_rental_for_after_confirmation(): void
    {
        $user = $this->customerUser();
        $this->cartReady($user);
        $this->fakeDoku();

        $this->post(route('cars.submit'), ['_token' => csrf_token(), 'phone_number' => '+60123456789', 'pay_option' => 'deposit'])
            ->assertRedirect(self::PAGE);

        $booking = Reservation::where('customer_id', $user->customer_id)->firstOrFail();
        $transaction = PaymentTransaction::where('reservation_id', $booking->id)->firstOrFail();
        $this->assertSame(Reservation::STATUS_AWAITING_PAYMENT, $booking->status);
        $this->assertEquals(200, (float) $transaction->amount);
        $this->assertEquals(['payment' => 0, 'deposit' => 200], $transaction->payload['parts']);
        Http::assertSent(function (HttpRequest $request) {
            $data = json_decode($request->body(), true);

            return $data['order']['amount'] === 200 && count($data['order']['line_items']) === 1
                && str_contains($data['order']['line_items'][0]['name'], 'deposit');
        });
        $this->assertStringContainsString('deposit only', $booking->comments()->value('comment'));

        $this->notify($this->success($transaction))->assertOk();
        $booking->refresh();
        $this->assertSame('Quote', $booking->status);
        $this->assertSame(['Deposit / Authorization'], Payment::where('reservation_id', $booking->id)->pluck('payment_type')->all());

        // The rental is shown as still to pay, collected by staff (not online)
        $this->get(route('myaccount.index'))
            ->assertSee('Deposit paid · awaiting confirmation')
            ->assertSee('Balance to pay: RM 300.00')
            ->assertSee('at the counter when you pick up your car, or our staff will contact you about payment')
            ->assertDontSee('Pay balance')->assertDontSee('Fully paid');
        $this->get(route('cars.confirmed', $booking))->assertSee('Deposit paid')
            ->assertSee('The rental is paid at the counter when you pick up your car, or our staff will contact you about payment.');
        $this->post(route('payments.doku.pay', $booking), ['_token' => csrf_token()]);
        $this->assertSame(1, PaymentTransaction::where('reservation_id', $booking->id)->count(), 'no balance payment online');

        $booking->update(['status' => 'Open']);
        $this->get(route('myaccount.index'))->assertSee('Balance to pay: RM 300.00')->assertDontSee('Pay balance');

        // Only when paying the balance online is switched on: after staff confirm
        config(['services.doku.pay_balance_online' => true]);
        $this->get(route('myaccount.index'))->assertSee('Pay balance RM 300.00');
        $balance = $this->startPayment($user, $booking);
        $this->assertEquals(300, (float) $balance->amount);
        $this->assertEquals(['payment' => 300, 'deposit' => 0], $balance->payload['parts']);
    }

    public function test_full_payment_stays_paid_when_staff_raise_the_price_before_confirming(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user);
        $this->notify($this->success($this->startPayment($user, $booking)))->assertOk();

        $booking->refresh()->update(['total_price' => 420]);
        $this->get(route('myaccount.index'))
            ->assertSee('Paid · awaiting confirmation')->assertDontSee('Deposit paid')
            ->assertSee('Balance to pay: RM 120.00')->assertDontSee('Pay balance');
    }

    /** A customer staff created without an email (login without one too). */
    private function userWithoutEmail(array $extra = []): User
    {
        $user = $this->customerUser(['email' => null] + $extra);
        $user->forceFill(['email' => null])->save();

        return $user->fresh();
    }

    public function test_customer_without_email_must_add_it_to_pay_online(): void
    {
        $user = $this->userWithoutEmail();
        $taken = $this->customerUser()->email;
        $this->cartReady($user);
        $this->fakeDoku();
        $send = fn (array $extra) => $this->post(route('cars.submit'), $extra + ['_token' => csrf_token(), 'phone_number' => '+60123456789']);

        $this->get(route('cars.fillinfo'))->assertOk()->assertSee('name="email"', false);
        $send([])->assertSessionHasErrors('email');
        $send(['email' => 'not-an-email'])->assertSessionHasErrors('email');
        $send(['email' => $taken])->assertSessionHasErrors('email');
        $send(['pay_option' => 'deposit'])->assertSessionHasErrors('email');
        $this->assertSame(0, Reservation::where('customer_id', $user->customer_id)->count());

        $email = 'zznew' . uniqid() . '@example.com';
        $send(['email' => $email])->assertRedirect(self::PAGE);
        $this->assertSame($email, Customer::find($user->customer_id)->email);
        $this->assertSame($email, $user->fresh()->email);
        Http::assertSent(fn (HttpRequest $request) => json_decode($request->body(), true)['customer']['email'] === $email);
    }

    public function test_a_customer_with_an_email_cannot_change_it_when_booking(): void
    {
        $user = $this->customerUser();
        $this->cartReady($user);
        $this->fakeDoku();

        $this->get(route('cars.fillinfo'))->assertDontSee('name="email"', false)->assertSee($user->email);
        $this->post(route('cars.submit'), ['_token' => csrf_token(), 'phone_number' => '+60123456789', 'email' => 'other@example.com'])->assertRedirect(self::PAGE);
        $this->assertSame($user->email, Customer::find($user->customer_id)->email);
    }

    public function test_book_now_pay_later_does_not_need_an_email(): void
    {
        $user = $this->userWithoutEmail(['deposit_required' => false]);
        $this->cartReady($user);

        $this->post(route('cars.submit'), ['_token' => csrf_token(), 'phone_number' => '+60123456789', 'pay_option' => 'deposit', 'email' => ''])
            ->assertSessionHasNoErrors();
        $this->assertSame('Quote', Reservation::where('customer_id', $user->customer_id)->value('status'));
    }

    public function test_unpaid_booking_without_email_asks_for_it_before_paying(): void
    {
        $user = $this->userWithoutEmail();
        $booking = $this->booking($user);
        $this->fakeDoku();

        $this->actingAs($user)->get(route('cars.confirmed', $booking))->assertSee('Your email (required)');
        $this->post(route('payments.doku.pay', $booking), ['_token' => csrf_token()])->assertSessionHas('payment_error');
        Http::assertNothingSent();

        $email = 'zznew' . uniqid() . '@example.com';
        $this->post(route('payments.doku.pay', $booking), ['_token' => csrf_token(), 'email' => $email])->assertRedirect(self::PAGE);
        $this->assertSame($email, Customer::find($user->customer_id)->email);
        Http::assertSent(fn (HttpRequest $request) => json_decode($request->body(), true)['customer']['email'] === $email);
    }

    public function test_unpaid_booking_can_still_be_paid_in_full_or_deposit_only(): void
    {
        $user = $this->customerUser();
        $booking = $this->booking($user);

        $this->actingAs($user)->get(route('myaccount.index'))
            ->assertSee('Pay RM 500.00 to complete booking')
            ->assertSee('Pay deposit only RM 200.00');

        $this->fakeDoku();
        $this->post(route('payments.doku.pay', $booking), ['_token' => csrf_token(), 'pay_option' => 'deposit'])->assertRedirect(self::PAGE);
        $this->assertEquals(200, (float) PaymentTransaction::where('reservation_id', $booking->id)->latest('id')->value('amount'));
    }

    public function test_customer_without_deposit_can_book_now_and_pay_later(): void
    {
        $user = $this->customerUser(['deposit_required' => false]);
        $this->cartReady($user);
        $this->fakeDoku();

        $this->get(route('cars.fillinfo'))
            ->assertSee('Book now, pay later')
            ->assertSee('Nothing to pay now')
            ->assertDontSee('Pay deposit only');

        $this->post(route('cars.submit'), ['_token' => csrf_token(), 'phone_number' => '+60123456789', 'pay_option' => 'deposit']);

        $booking = Reservation::where('customer_id', $user->customer_id)->firstOrFail();
        Http::assertNothingSent();
        $this->assertSame('Quote', $booking->status);
        $this->assertNotEmpty($booking->quotation_no);
        $this->assertSame(0, Payment::where('reservation_id', $booking->id)->count());
        $marker = PaymentTransaction::where('reservation_id', $booking->id)->sole();
        $this->assertSame(['website', 'pay_later', 0.0], [$marker->gateway, $marker->status, (float) $marker->amount]);
        $this->assertStringContainsString('pay later', $booking->comments()->value('comment'));

        $this->get(route('cars.confirmed', $booking))->assertOk()->assertSee('Booking received')->assertDontSee('Complete your payment');
        $this->get(route('myaccount.index'))
            ->assertSee('Booked · awaiting confirmation')
            ->assertSee('Balance to pay: RM 300.00')
            ->assertDontSee('Pay balance');
        $this->get(route('cars.confirmed', $booking))->assertSee('Nothing to pay now: pay at the counter when you pick up your car, or our staff will contact you about payment.');

        $booking->update(['status' => 'Open']);
        $this->get(route('myaccount.index'))->assertSee('Balance to pay: RM 300.00')->assertDontSee('Pay balance');
    }

    public function test_full_amount_is_the_default_and_the_only_option_when_switched_off(): void
    {
        config(['services.doku.allow_deposit_only' => false]);
        $user = $this->customerUser();
        $this->cartReady($user);
        $this->fakeDoku();

        $this->get(route('cars.fillinfo'))->assertDontSee('How would you like to pay?')->assertSee('Pay RM 500.00 &amp; Book', false);
        // A sent "deposit" is ignored: the full amount is charged
        $this->post(route('cars.submit'), ['_token' => csrf_token(), 'phone_number' => '+60123456789', 'pay_option' => 'deposit']);
        $booking = Reservation::where('customer_id', $user->customer_id)->firstOrFail();
        $this->assertEquals(500, (float) PaymentTransaction::where('reservation_id', $booking->id)->value('amount'));
        $this->get(route('myaccount.index'))->assertDontSee('Pay deposit only');
    }
}
