<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Location;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\Reservation;
use App\Models\User;
use App\Models\VehicleClass;
use App\Models\VehicleModel;
use Bouncer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * The local-only fake DOKU (DOKU_FAKE=true): the whole pay & book flow without DOKU or keys.
 *
 * Runs against the database shared with rngcarrental: every test is rolled back (DatabaseTransactions).
 * Never switch this to RefreshDatabase.
 */
class FakeDokuTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Fake mode with no keys at all, as on a developer's PC before SenangPay sends them
        config(['services.doku' => [
            'enabled' => true, 'fake' => true, 'client_id' => null, 'secret_key' => null,
            'base_url' => 'https://api-sandbox.doku.com', 'currency' => 'MYR', 'due_minutes' => 60,
            'methods' => [], 'pay_statuses' => ['Open', 'Rental'],
        ]]);
        Http::preventStrayRequests(); // nothing may go to DOKU
    }

    private function customerUser(): User
    {
        $username = 'zztest' . uniqid();
        $customer = Customer::create([
            'first_name' => 'Fake', 'last_name' => 'Pay', 'username' => $username, 'email' => "$username@example.com",
            'password' => Hash::make('Secret#12345'), 'phone_number' => '+60123456789',
            'id_type' => 'ic', 'ic' => '900101-13-5555', 'expiration_date' => now()->addYear()->toDateString(),
        ]);
        $user = new User();
        $user->forceFill([
            'name' => 'Fake Pay', 'username' => $username, 'email' => $customer->email, 'password' => $customer->password,
            'customer_id' => $customer->id, 'role_id' => Bouncer::role()->where('name', 'customer')->value('id'), 'is_active' => 1,
        ])->save();

        return $user;
    }

    /** Pay & book a 3-day car at RM 100/day; returns the pending transaction (DOKU page = the fake page). */
    private function payAndBook(User $user): PaymentTransaction
    {
        $class = VehicleClass::create(['name' => 'ZZ Class ' . uniqid(), 'is_active' => 1, 'daily_rate' => 100, 'hourly_rate' => 10, 'insurance' => 20]);
        $car = VehicleModel::create(['name' => 'ZZ Car ' . uniqid(), 'vehicle_class_id' => $class->id, 'is_active' => 1, 'show_on_frontend' => 1]);
        $branch = Location::create(['name' => 'ZZ Branch ' . uniqid(), 'is_active' => 1, 'is_airport' => 0]);

        $this->actingAs($user);
        $this->get(route('cars.select', $car));
        $this->post(route('cars.cart.update'), [
            '_token' => csrf_token(), 'pickup_location_id' => $branch->id, 'return_location_id' => $branch->id,
            'pickup_date' => now()->addDays(7)->toDateString(), 'pickup_time' => '10:00',
            'return_date' => now()->addDays(10)->toDateString(), 'return_time' => '10:00',
        ]);
        $this->get(route('cars.fillinfo'))->assertSee('Pay RM 500.00 &amp; Book', false);

        $response = $this->post(route('cars.submit'), ['_token' => csrf_token(), 'phone_number' => '+60123456789']);
        $transaction = PaymentTransaction::whereHas('reservation', fn ($q) => $q->where('customer_id', $user->customer_id))->latest('id')->firstOrFail();
        $response->assertRedirect(route('payments.fake-doku.show', $transaction->reference));

        return $transaction;
    }

    private function choose(PaymentTransaction $transaction, string $action)
    {
        return $this->post(route('payments.fake-doku.act', $transaction->reference), ['_token' => csrf_token(), 'action' => $action]);
    }

    public function test_fake_page_shows_the_payment(): void
    {
        $transaction = $this->payAndBook($this->customerUser());

        $this->get(route('payments.fake-doku.show', $transaction->reference))->assertOk()
            ->assertSee('FAKE DOKU · LOCAL TESTING ONLY · NO REAL PAYMENT')
            ->assertSee('Car rental')->assertSee('RM 300.00')
            ->assertSee('Refundable security deposit')->assertSee('RM 200.00')
            ->assertSee('MYR 500.00')
            ->assertSee('Pay (success)');
    }

    public function test_pay_runs_the_real_notification_and_records_everything(): void
    {
        $user = $this->customerUser();
        $transaction = $this->payAndBook($user);
        $booking = $transaction->reservation;

        $this->choose($transaction, 'pay')->assertRedirect(route('payments.doku.result', [$booking, $transaction]));
        $this->get(route('payments.doku.result', [$booking, $transaction]))->assertSee('Payment received');

        $this->assertSame('Quote', $booking->fresh()->status);
        $this->assertSame('paid', $transaction->fresh()->status);
        $this->assertSame(['Payment', 'Deposit / Authorization'], Payment::where('reservation_id', $booking->id)->orderBy('id')->pluck('payment_type')->all());
        $this->get(route('myaccount.index'))->assertSee('Paid · awaiting confirmation');
        Http::assertNothingSent();
    }

    public function test_late_message_is_picked_up_by_the_result_page(): void
    {
        $user = $this->customerUser();
        $transaction = $this->payAndBook($user);

        $this->choose($transaction, 'pay_delayed');
        $this->assertSame('pending', $transaction->fresh()->status, 'no message yet');

        $this->get(route('payments.doku.result', [$transaction->reservation_id, $transaction]))->assertSee('Payment received');
        $this->assertSame('paid', $transaction->fresh()->status);
        $this->assertSame('Quote', $transaction->reservation->fresh()->status);
    }

    public function test_declined_card_can_try_again(): void
    {
        $user = $this->customerUser();
        $transaction = $this->payAndBook($user);

        $this->from(route('payments.fake-doku.show', $transaction->reference))->choose($transaction, 'fail')
            ->assertRedirect(route('payments.fake-doku.show', $transaction->reference));
        $this->get(route('payments.fake-doku.show', $transaction->reference))->assertSee('Card declined (fake)');
        $this->assertSame('pending', $transaction->fresh()->status);

        $this->choose($transaction, 'pay');
        $this->assertSame('paid', $transaction->fresh()->status);
    }

    public function test_expired_page_then_pay_later_with_a_new_page(): void
    {
        $user = $this->customerUser();
        $transaction = $this->payAndBook($user);
        $booking = $transaction->reservation;

        $this->choose($transaction, 'expire');
        $this->assertSame('expired', $transaction->fresh()->status);
        $this->get(route('payments.doku.result', [$booking, $transaction]))->assertSee('Payment time ran out');
        $this->get(route('myaccount.index'))->assertSee('Payment not completed')->assertSee('Pay RM 500.00 to complete booking');

        $this->post(route('payments.doku.pay', $booking), ['_token' => csrf_token()]);
        $new = PaymentTransaction::where('reservation_id', $booking->id)->latest('id')->first();
        $this->assertNotSame($transaction->id, $new->id);
        $this->choose($new, 'pay');
        $this->assertSame('Quote', $booking->fresh()->status);
    }

    public function test_back_without_paying_leaves_it_unpaid(): void
    {
        $user = $this->customerUser();
        $transaction = $this->payAndBook($user);

        $this->choose($transaction, 'cancel');
        $this->get(route('payments.doku.result', [$transaction->reservation_id, $transaction]))->assertSee('Waiting for payment confirmation');
        $this->assertSame(Reservation::STATUS_AWAITING_PAYMENT, $transaction->reservation->fresh()->status);
    }

    public function test_notifications_must_still_be_signed(): void
    {
        $transaction = $this->payAndBook($this->customerUser());

        $this->call('POST', '/payments/doku/notify', [], [], [], [
            'CONTENT_TYPE' => 'application/json', 'HTTP_CLIENT_ID' => 'FAKE-DOKU-CLIENT', 'HTTP_REQUEST_ID' => 'r',
            'HTTP_REQUEST_TIMESTAMP' => 't', 'HTTP_SIGNATURE' => 'HMACSHA256=made-up',
        ], json_encode(['order' => ['invoice_number' => $transaction->reference, 'amount' => 500], 'transaction' => ['status' => 'SUCCESS']]))
            ->assertStatus(401);
        $this->assertSame('pending', $transaction->fresh()->status);
    }

    public function test_never_on_a_server(): void
    {
        $transaction = $this->payAndBook($this->customerUser());

        $this->app['env'] = 'production';
        $this->get(route('payments.fake-doku.show', $transaction->reference))->assertNotFound();
        $this->choose($transaction, 'pay')->assertNotFound();
        $this->assertSame('pending', $transaction->fresh()->status);
        // Without real keys, online payment is simply off there
        $this->assertFalse(\App\Payments\BookingPayment::enabled());
    }
}
