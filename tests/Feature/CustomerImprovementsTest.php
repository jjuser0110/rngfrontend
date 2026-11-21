<?php

namespace Tests\Feature;

use App\Models\ChargeType;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Reservation;
use App\Models\User;
use App\Models\VehicleClass;
use App\Models\VehicleModel;
use Bouncer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * IC / passport numbers, several licence files, expired licences, no-deposit customers, the driver request
 * and invoice downloads.
 *
 * Runs against the database shared with rngcarrental: every test is rolled back (DatabaseTransactions).
 * Never switch this to RefreshDatabase.
 */
class CustomerImprovementsTest extends TestCase
{
    use DatabaseTransactions;

    private const PASSWORD = 'Secret#12345';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function customerUser(array $customerExtra = []): User
    {
        $username = 'zztest' . uniqid();
        $customer = Customer::create([
            'first_name' => 'Test', 'last_name' => 'Customer', 'username' => $username, 'email' => "$username@example.com",
            'password' => Hash::make(self::PASSWORD), 'phone_number' => '+60123456789',
            'id_type' => 'ic', 'ic' => '900101-13-5555', 'expiration_date' => now()->addYear()->toDateString(),
        ]);
        if ($customerExtra) {
            DB::table('customers')->where('id', $customer->id)->update($customerExtra); // staff-only fields included
        }

        $user = new User();
        $user->forceFill([
            'name' => 'Test Customer', 'username' => $username, 'email' => $customer->email, 'password' => $customer->password,
            'customer_id' => $customer->id, 'role_id' => Bouncer::role()->where('name', 'customer')->value('id'), 'is_active' => 1,
        ])->save();

        return $user;
    }

    private function registration(array $override = []): array
    {
        $username = 'zzreg' . uniqid();

        return $override + [
            '_token' => csrf_token(), 'username' => $username, 'email' => "$username@example.com",
            'password' => self::PASSWORD, 'password_confirmation' => self::PASSWORD,
            'first_name' => 'Reg', 'last_name' => 'Test', 'phone_number' => '+60123456789',
            'date_of_birth' => '1990-01-01', 'id_type' => 'ic', 'ic' => '900101-13-5555',
            'expiration_date' => now()->addYears(3)->toDateString(),
        ];
    }

    private function upload(int $count = 1)
    {
        return $this->post(route('attachments.store'), [
            '_token' => csrf_token(),
            'uuid' => session('last_upload_uuid'),
            'attachments' => array_map(fn ($i) => UploadedFile::fake()->image("doc$i.png"), range(1, $count)),
        ]);
    }

    /** Logged-in customer with a car chosen and the cart done. Returns the car. */
    private function cartReady(User $user, array $charges = []): VehicleModel
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
            'charges' => $charges,
        ])->assertRedirect(route('cars.fillinfo'));

        return $car;
    }

    // ---------------------------------------------------------------- IC / passport

    public function test_register_with_a_malaysian_ic_saves_it_with_dashes(): void
    {
        $this->post('/register', $this->registration(['ic' => '201003135111']))->assertSessionHasNoErrors();

        $customer = Customer::find(auth()->user()->customer_id);
        $this->assertSame('201003-13-5111', $customer->ic);
        $this->assertSame('ic', $customer->id_type);
    }

    public function test_register_rejects_a_badly_formed_ic(): void
    {
        $this->post('/register', $this->registration(['ic' => '12345']))->assertSessionHasErrors('ic');
        $this->post('/register', $this->registration(['ic' => '201399-13-5111']))->assertSessionHasErrors('ic'); // month 13
        $this->post('/register', $this->registration(['ic' => 'ABCDEF-13-5111']))->assertSessionHasErrors('ic');
        $this->assertGuest();
    }

    public function test_step_3_checks_the_ic_before_the_form_is_sent(): void
    {
        $this->get(route('register'));
        $check = fn (array $fields) => $this->postJson(route('register.validate-step'), $fields + [
            '_token' => csrf_token(), 'step' => 3, 'expiration_date' => now()->addYear()->toDateString(),
        ]);

        $check(['id_type' => 'ic', 'ic' => '12345'])->assertStatus(422)
            ->assertJsonPath('errors.ic.0', 'Please enter the IC number as YYMMDD-PB-#### (e.g. 201003-13-5111).');
        $check(['id_type' => 'passport', 'ic' => 'A1-2'])->assertStatus(422)->assertJsonValidationErrors('ic');
        $check(['id_type' => 'ic', 'ic' => '201003-13-5111'])->assertOk()->assertJson(['valid' => true]);
    }

    public function test_a_server_error_reopens_the_step_that_has_it(): void
    {
        $this->get(route('register'));
        $html = $this->followingRedirects()->post('/register', $this->registration(['ic' => '12345']))->getContent();

        $this->assertStringContainsString('Please enter the IC number as YYMMDD-PB-####', $html);
        $this->assertStringContainsString('showStep(', $html, 'the page opens the step with the error');
    }

    public function test_foreign_customer_registers_with_a_passport(): void
    {
        $this->post('/register', $this->registration(['id_type' => 'passport', 'ic' => 'a1234 5678']))->assertSessionHasNoErrors();

        $customer = Customer::find(auth()->user()->customer_id);
        $this->assertSame('A12345678', $customer->ic);
        $this->assertSame('passport', $customer->id_type);
    }

    public function test_passport_must_be_letters_and_numbers(): void
    {
        $this->post('/register', $this->registration(['id_type' => 'passport', 'ic' => 'A1-2']))->assertSessionHasErrors('ic');
        $this->post('/register', $this->registration(['id_type' => 'other', 'ic' => 'A12345678']))->assertSessionHasErrors('id_type');
    }

    public function test_profile_change_to_passport(): void
    {
        $user = $this->customerUser();
        $this->actingAs($user);

        $this->put(route('myaccount.profile.update'), [
            '_token' => csrf_token(), 'first_name' => 'A', 'last_name' => 'B', 'phone_number' => '+60123456789',
            'date_of_birth' => '1990-01-01', 'id_type' => 'passport', 'ic' => 'K9876543', 'expiration_date' => now()->addYear()->toDateString(),
        ])->assertSessionHasNoErrors();

        $this->assertSame('passport', Customer::find($user->customer_id)->id_type);
    }

    // ---------------------------------------------------------------- several files

    public function test_register_keeps_several_files(): void
    {
        $this->get(route('register'));
        $uuid = $this->upload(2)->assertOk()->json('uuid');
        $this->post(route('attachments.store'), ['_token' => csrf_token(), 'uuid' => $uuid, 'attachments' => [UploadedFile::fake()->image('ic.png')]])
            ->assertOk()->assertJson(['uuid' => $uuid]);

        $this->post('/register', $this->registration(['temp_uuid' => $uuid]))->assertSessionHasNoErrors();

        $this->assertCount(3, Customer::find(auth()->user()->customer_id)->getMedia('attachments'));
    }

    public function test_no_more_than_five_files(): void
    {
        $this->get(route('register'));
        $uuid = $this->upload(5)->assertOk()->json('uuid');

        $this->postJson(route('attachments.store'), ['_token' => csrf_token(), 'uuid' => $uuid, 'attachments' => [UploadedFile::fake()->image('six.png')]])
            ->assertStatus(422)->assertJsonPath('message', 'You can upload up to 5 files.');
    }

    public function test_profile_adds_files_and_removes_only_its_own(): void
    {
        $user = $this->customerUser();
        $customer = Customer::find($user->customer_id);
        $customer->addMedia(UploadedFile::fake()->image('licence.png'))->toMediaCollection('attachments');
        $removed = $customer->addMedia(UploadedFile::fake()->image('old.png'))->toMediaCollection('attachments');
        $someoneElses = Customer::find($this->customerUser()->customer_id)->addMedia(UploadedFile::fake()->image('x.png'))->toMediaCollection('attachments');

        $this->actingAs($user);
        $this->get(route('myaccount.index'))->assertOk()->assertSee('licence.png')->assertDontSee('sample_driving_license');
        $uuid = $this->upload(1)->assertOk()->json('uuid');

        $this->put(route('myaccount.profile.update'), [
            '_token' => csrf_token(), 'first_name' => 'A', 'last_name' => 'B', 'phone_number' => '+60123456789',
            'date_of_birth' => '1990-01-01', 'id_type' => 'ic', 'ic' => '900101-13-5555', 'expiration_date' => now()->addYear()->toDateString(),
            'temp_uuid' => $uuid, 'remove_media' => [$removed->id, $someoneElses->id],
        ])->assertSessionHasNoErrors();

        $names = $customer->fresh()->getMedia('attachments')->pluck('file_name')->all();
        $this->assertEqualsCanonicalizing(['licence.png', 'doc1.png'], $names, 'old file removed, new file added, licence kept');
        $this->assertDatabaseHas('media', ['id' => $someoneElses->id]);
    }

    // ---------------------------------------------------------------- expired licence

    public function test_expired_licence_can_register(): void
    {
        $this->post('/register', $this->registration(['expiration_date' => now()->subMonth()->toDateString()]))->assertSessionHasNoErrors();
        $this->assertAuthenticated();
    }

    public function test_expired_licence_cannot_book(): void
    {
        $user = $this->customerUser(['expiration_date' => now()->subDay()->toDateString()]);
        $this->cartReady($user);

        $this->get(route('cars.fillinfo'))->assertOk()->assertSee('Your driving licence has expired')->assertDontSee('Send Booking Request');
        $this->post(route('cars.submit'), ['_token' => csrf_token(), 'phone_number' => '+60123456789'])->assertSessionHasErrors();
        $this->assertSame(0, Reservation::where('customer_id', $user->customer_id)->count());
    }

    public function test_licence_expiring_today_can_still_book(): void
    {
        $user = $this->customerUser(['expiration_date' => now()->toDateString()]);
        $this->cartReady($user);

        $this->get(route('cars.fillinfo'))->assertOk()->assertDontSee('Your driving licence has expired');
        $this->post(route('cars.submit'), ['_token' => csrf_token(), 'phone_number' => '+60123456789']);
        $this->assertSame(1, Reservation::where('customer_id', $user->customer_id)->count());
    }

    // ---------------------------------------------------------------- deposit per customer

    public function test_customer_without_deposit_books_with_none(): void
    {
        $user = $this->customerUser(['deposit_required' => 0]);
        $this->cartReady($user);

        $this->get(route('cars.fillinfo'))->assertSee('No security deposit needed for your account.');
        $this->post(route('cars.submit'), ['_token' => csrf_token(), 'phone_number' => '+60123456789']);
        $this->assertEquals(0, (float) Reservation::where('customer_id', $user->customer_id)->value('security_deposit'));
    }

    public function test_customer_with_deposit_books_with_the_usual_one(): void
    {
        $user = $this->customerUser();
        $this->cartReady($user);
        $this->post(route('cars.submit'), ['_token' => csrf_token(), 'phone_number' => '+60123456789']);
        $this->assertEquals((float) config('reservation.security_deposit'), (float) Reservation::where('customer_id', $user->customer_id)->value('security_deposit'));
    }

    // ---------------------------------------------------------------- driver request

    public function test_driver_is_a_tick_with_no_price(): void
    {
        $driver = ChargeType::where('name', ChargeType::DRIVER)->firstOrFail();
        $driver->update(['show_on_frontend' => 1]);
        $user = $this->customerUser();
        $this->cartReady($user, [$driver->id => 3]); // even if a quantity is sent, it is one request

        $html = $this->get(route('cars.cart'))->assertOk()->getContent();
        $this->assertStringContainsString('type="checkbox" name="charges[' . $driver->id . ']"', $html, 'a tick box, not a quantity');
        $this->assertStringNotContainsString('+RM ' . number_format($driver->amount, 2), $html, 'no driver price shown');

        $this->post(route('cars.submit'), ['_token' => csrf_token(), 'phone_number' => '+60123456789']);
        $reservation = Reservation::where('customer_id', $user->customer_id)->firstOrFail();
        $line = $reservation->chargeDetails->firstWhere('name', ChargeType::DRIVER);
        $this->assertEquals(0, (float) $line->total_charge, 'staff set the price');
        $this->assertSame(1, (int) $line->quantity);
        $this->assertEquals(300, (float) $reservation->total_price, 'only the rental: 3 days x RM 100');
        $this->assertTrue($reservation->comments()->where('comment', 'like', '%Driver requested%')->exists());
    }

    // ---------------------------------------------------------------- invoice

    private function bookingFor(User $user, array $extra = []): Reservation
    {
        $id = DB::table('reservations')->insertGetId($extra + [
            'customer_id' => $user->customer_id, 'company_id' => 1, 'status' => 'Open', 'invoice_no' => 'INVZZ-' . uniqid(),
            'pickup_date' => now()->toDateString(), 'pickup_time' => '10:00', 'return_date' => now()->addDays(2)->toDateString(), 'return_time' => '10:00',
            'total_price' => 250, 'created_by' => $user->id, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return Reservation::findOrFail($id);
    }

    public function test_customer_downloads_their_invoice_when_open_on_rent_or_closed(): void
    {
        $user = $this->customerUser();
        $this->actingAs($user);

        foreach (['Open', 'Rental', 'Completed'] as $status) {
            $booking = $this->bookingFor($user, ['status' => $status]);
            $response = $this->get(route('myaccount.invoice', $booking))->assertOk();
            $this->assertSame('application/pdf', $response->headers->get('content-type'));
            $this->assertStringStartsWith('%PDF', $response->getContent());
        }

        $this->get(route('myaccount.index'))->assertSee('Download invoice');
    }

    public function test_no_invoice_for_requests_cancelled_or_someone_elses(): void
    {
        $user = $this->customerUser();
        $this->actingAs($user);

        $this->get(route('myaccount.invoice', $this->bookingFor($user, ['status' => 'Quote'])))->assertNotFound();
        $this->get(route('myaccount.invoice', $this->bookingFor($user, ['status' => 'Cancelled'])))->assertNotFound();
        $this->get(route('myaccount.invoice', $this->bookingFor($user, ['invoice_no' => null])))->assertNotFound();
        $this->get(route('myaccount.invoice', $this->bookingFor($this->customerUser())))->assertNotFound();
    }
}
