<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Location;
use App\Models\User;
use App\Models\VehicleClass;
use App\Models\VehicleModel;
use Bouncer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * The home page search (places, dates, times) must reach the cart, whether the customer is already
 * logged in, logs in, or registers on the way.
 *
 * Runs against the database shared with rngcarrental: every test is rolled back (DatabaseTransactions).
 * Never switch this to RefreshDatabase.
 */
class BookingScheduleFlowTest extends TestCase
{
    use DatabaseTransactions;

    private const PASSWORD = 'Secret#12345';

    private VehicleModel $vehicleModel;
    private Location $branch;
    private array $search;

    protected function setUp(): void
    {
        parent::setUp();

        $class = VehicleClass::create(['name' => 'TEST Class ' . uniqid(), 'is_active' => 1, 'daily_rate' => 150, 'hourly_rate' => 20]);
        $this->vehicleModel = VehicleModel::create([
            'name' => 'TEST Model ' . uniqid(), 'vehicle_class_id' => $class->id, 'is_active' => 1, 'show_on_frontend' => 1,
            'daily_rate' => 111, 'hourly_rate' => 15, 'airport_daily_rate' => 121, 'airport_hourly_rate' => 17,
        ]);
        $this->branch = Location::create(['name' => 'TEST Branch ' . uniqid(), 'is_active' => 1, 'is_airport' => 0]);

        $this->search = [
            'vehicle_type' => 'car_bike',
            'pickup_place' => 'PINES SQUARE, Jalan Batu Kawa, Kuching',
            'return_place' => $this->branch->name,
            'pickup_date'  => now()->addDays(3)->toDateString(),
            'pickup_time'  => '14:30',
            'return_date'  => now()->addDays(6)->toDateString(),
            'return_time'  => '16:00',
        ];
    }

    private function makeCustomerUser(): User
    {
        $username = 'zztest' . uniqid();
        $customer = Customer::create([
            'first_name' => 'Test', 'last_name' => 'Flow', 'username' => $username, 'email' => "$username@example.com",
            'password' => Hash::make(self::PASSWORD), 'phone_number' => '+60123456789',
        ]);

        $user = new User();
        $user->name        = 'Test Flow';
        $user->username    = $username;
        $user->email       = $customer->email;
        $user->password    = $customer->password;
        $user->customer_id = $customer->id;
        $user->role_id     = Bouncer::role()->where('name', 'customer')->value('id');
        $user->is_active   = 1;
        $user->save();

        return $user;
    }

    private function searchFromHomePage(): void
    {
        $this->get(route('welcome'))->assertOk();
        $this->post(route('cars.search'), $this->search + ['_token' => csrf_token()])
            ->assertRedirect(route('cars.index'));
        $this->get(route('cars.index'))->assertOk();
    }

    private function login(User $user)
    {
        return $this->post('/login', ['username' => $user->username, 'password' => self::PASSWORD, '_token' => csrf_token()]);
    }

    private function assertCartShowsSearch(): void
    {
        $html = preg_replace('/\s+/', ' ', $this->get(route('cars.cart'))->assertOk()->getContent());

        $this->assertStringContainsString('value="' . $this->search['pickup_date'] . '"', $html, 'pick-up date');
        $this->assertStringContainsString('value="' . $this->search['return_date'] . '"', $html, 'drop-off date');
        $this->assertStringContainsString('<option value="14:30" selected>', $html, 'pick-up time');
        $this->assertStringContainsString('<option value="16:00" selected>', $html, 'drop-off time');
        $this->assertStringContainsString('<option value="0" selected>', $html, 'pick-up is "Other address"');
        $this->assertStringContainsString('value="' . e($this->search['pickup_place']) . '"', $html, 'pick-up address');
        $this->assertMatchesRegularExpression('/<option value="' . $this->branch->id . '" selected>/', $html, 'drop-off branch');
    }

    public function test_website_has_its_own_session_cookie(): void
    {
        // rngcarrental uses "laravel_session"; sharing it on localhost logs each app out of the other
        $this->assertSame('rngfrontend_session', config('session.cookie'));
    }

    public function test_logged_in_customer_keeps_the_search_in_the_cart(): void
    {
        $this->actingAs($this->makeCustomerUser());
        $this->searchFromHomePage();

        $this->get(route('cars.select', $this->vehicleModel))->assertRedirect(route('cars.cart'));
        $this->assertCartShowsSearch();
    }

    public function test_guest_who_logs_in_keeps_the_search_in_the_cart(): void
    {
        $user = $this->makeCustomerUser();
        $this->searchFromHomePage();

        $this->get(route('cars.select', $this->vehicleModel))->assertRedirect(route('login'));
        $this->login($user)->assertRedirect(route('cars.select', $this->vehicleModel));
        $this->get(route('cars.select', $this->vehicleModel))->assertRedirect(route('cars.cart'));
        $this->assertCartShowsSearch();
    }

    public function test_guest_who_registers_keeps_the_search_in_the_cart(): void
    {
        $this->searchFromHomePage();
        $this->get(route('cars.select', $this->vehicleModel))->assertRedirect(route('login'));
        $this->get(route('register'))->assertOk();

        $username = 'zztest' . uniqid();
        $this->post('/register', [
            '_token' => csrf_token(), 'username' => $username, 'email' => "$username@example.com",
            'password' => self::PASSWORD, 'password_confirmation' => self::PASSWORD,
            'first_name' => 'Test', 'last_name' => 'Flow', 'phone_number' => '+60123456789',
            'date_of_birth' => '1990-01-01', 'ic' => '900101-13-5555', 'expiration_date' => now()->addYears(3)->toDateString(),
        ])->assertRedirect(route('cars.select', $this->vehicleModel));

        $this->get(route('cars.select', $this->vehicleModel))->assertRedirect(route('cars.cart'));
        $this->assertCartShowsSearch();
    }

    public function test_cart_changes_are_kept_when_changing_the_car(): void
    {
        $this->actingAs($this->makeCustomerUser());
        $this->searchFromHomePage();
        $this->get(route('cars.select', $this->vehicleModel));

        // In the cart: drop-off moves to another branch and the return date changes (the live price saves it)
        $otherBranch = Location::create(['name' => 'TEST Other Branch ' . uniqid(), 'is_active' => 1, 'is_airport' => 0]);
        $newReturnDate = now()->addDays(8)->toDateString();
        $this->postJson(route('cars.quote'), [
            'pickup_location_id' => 0, 'pickup_address' => $this->search['pickup_place'],
            'return_location_id' => $otherBranch->id,
            'pickup_date' => $this->search['pickup_date'], 'pickup_time' => '14:30',
            'return_date' => $newReturnDate, 'return_time' => '16:00',
        ])->assertOk();

        // "Change car": the trip bar shows the cart's drop-off, not the home page search
        $fleet = $this->get(route('cars.index'))->assertOk()->getContent();
        $this->assertStringContainsString(e($otherBranch->name), $fleet);
        $this->assertStringNotContainsString(e($this->branch->name), $fleet);

        // ...and the next car's cart opens with the same changes
        $this->get(route('cars.select', $this->vehicleModel))->assertRedirect(route('cars.cart'));
        $cart = preg_replace('/\s+/', ' ', $this->get(route('cars.cart'))->getContent());
        $this->assertMatchesRegularExpression('/<option value="' . $otherBranch->id . '" selected>/', $cart);
        $this->assertStringContainsString('value="' . $newReturnDate . '"', $cart);
    }

    public function test_proceed_needs_an_address_for_other_address(): void
    {
        $this->actingAs($this->makeCustomerUser());
        $this->searchFromHomePage();
        $this->get(route('cars.select', $this->vehicleModel));
        $this->get(route('cars.cart'));

        // Drop-off address picked, then cleared again: the form sends "Other address" with no address
        $this->post(route('cars.cart.update'), [
            '_token' => csrf_token(),
            'pickup_location_id' => 0, 'pickup_address' => $this->search['pickup_place'],
            'return_location_id' => 0, 'return_address' => '',
            'pickup_date' => $this->search['pickup_date'], 'pickup_time' => '14:30',
            'return_date' => $this->search['return_date'], 'return_time' => '16:00',
        ])->assertRedirect(route('cars.cart'))->assertSessionHasErrors(['return_address' => 'Please enter the drop-off address.']);

        $this->get(route('cars.fillinfo'))->assertRedirect(route('cars.cart'));
    }

    public function test_menu_sign_in_returns_to_the_cart_when_a_car_is_chosen(): void
    {
        $user = $this->makeCustomerUser();
        $this->searchFromHomePage();
        $this->get(route('cars.select', $this->vehicleModel)); // guest: sent to login, car not kept yet
        session()->forget('url.intended'); // as if they left and used the "Sign In" button instead
        session(['booking' => session('booking') + ['vehicle_model_id' => $this->vehicleModel->id]]);

        $this->get(route('login'))->assertOk();
        $this->login($user)->assertRedirect(route('cars.cart'));
        $this->assertCartShowsSearch();
    }

    public function test_menu_sign_in_returns_to_the_fleet_after_a_search(): void
    {
        $user = $this->makeCustomerUser();
        $this->searchFromHomePage();

        $this->get(route('login'))->assertOk();
        $this->login($user)->assertRedirect(route('cars.index'));
    }

    public function test_menu_sign_in_without_a_booking_goes_to_my_account(): void
    {
        $user = $this->makeCustomerUser();

        $this->get(route('login'))->assertOk();
        $this->login($user)->assertRedirect(route('myaccount.index'));
    }
}
