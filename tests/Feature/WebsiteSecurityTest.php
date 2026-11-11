<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\TemporaryUpload;
use App\Models\User;
use Bouncer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Licence uploads, website login rules, sign-up for customers staff already added, and removed leftover pages.
 *
 * Runs against the database shared with rngcarrental: every test is rolled back (DatabaseTransactions).
 * Never switch this to RefreshDatabase.
 */
class WebsiteSecurityTest extends TestCase
{
    use DatabaseTransactions;

    private const PASSWORD = 'Secret#12345';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function customerUser(array $userExtra = []): User
    {
        $username = 'zztest' . uniqid();
        $customer = Customer::create([
            'first_name' => 'Test', 'last_name' => 'Customer', 'username' => $username, 'email' => "$username@example.com",
            'password' => Hash::make(self::PASSWORD), 'phone_number' => '+60123456789',
        ]);

        $user = new User();
        $user->forceFill($userExtra + [
            'name' => 'Test Customer', 'username' => $username, 'email' => $customer->email, 'password' => $customer->password,
            'customer_id' => $customer->id, 'role_id' => Bouncer::role()->where('name', 'customer')->value('id'), 'is_active' => 1,
        ])->save();

        return $user;
    }

    private function upload(array $extra = [])
    {
        return $this->post(route('attachments.store'), $extra + [
            '_token' => csrf_token(),
            'attachments' => [UploadedFile::fake()->image('licence.png')],
            'collection' => 'attachments',
        ]);
    }

    private function registration(array $override = []): array
    {
        $username = 'zzreg' . uniqid();

        return $override + [
            '_token' => csrf_token(), 'username' => $username, 'email' => "$username@example.com",
            'password' => self::PASSWORD, 'password_confirmation' => self::PASSWORD,
            'first_name' => 'Reg', 'last_name' => 'Test', 'phone_number' => '+60123456789',
            'date_of_birth' => '1990-01-01', 'ic' => '900101-13-5555', 'expiration_date' => now()->addYears(3)->toDateString(),
        ];
    }

    // ---------------------------------------------------------------- uploads

    public function test_upload_never_attaches_to_a_chosen_record(): void
    {
        $victim = $this->customerUser();
        $this->get(route('register'));

        $this->upload(['model_type' => Customer::class, 'model_id' => $victim->customer_id])->assertOk();

        $this->assertCount(0, Customer::find($victim->customer_id)->getMedia('attachments'));
    }

    public function test_upload_only_accepts_licence_file_types(): void
    {
        $this->get(route('register'));
        $this->post(route('attachments.store'), [
            '_token' => csrf_token(),
            'attachments' => [UploadedFile::fake()->create('script.php', 5, 'text/x-php')],
        ])->assertSessionHasErrors('attachments.0');
    }

    public function test_visitor_can_delete_their_own_unsaved_upload(): void
    {
        $this->get(route('register'));
        $id = $this->upload()->assertOk()->json('files.0.id');

        $this->delete(route('attachments.destroy', $id), ['_token' => csrf_token()])->assertOk();
        $this->assertDatabaseMissing('media', ['id' => $id]);
    }

    public function test_saved_files_cannot_be_deleted(): void
    {
        $customer = Customer::find($this->customerUser()->customer_id);
        $media = $customer->addMedia(UploadedFile::fake()->image('saved.png'))->toMediaCollection('attachments');

        $this->get(route('register'));
        $this->delete(route('attachments.destroy', $media->id), ['_token' => csrf_token()])->assertNotFound();
        $this->assertDatabaseHas('media', ['id' => $media->id]);
    }

    public function test_another_visitors_upload_cannot_be_deleted_or_used(): void
    {
        $this->get(route('register'));
        $response = $this->upload()->assertOk();
        $uuid = $response->json('uuid');
        $id = $response->json('files.0.id');

        // A different visitor (new session)
        $this->flushSession();
        $this->get(route('register'));
        $this->delete(route('attachments.destroy', $id), ['_token' => csrf_token()])->assertNotFound();
        $this->assertDatabaseHas('media', ['id' => $id]);

        $this->upload(['uuid' => $uuid])->assertOk()->assertJsonMissing(['uuid' => $uuid]);

        $this->post('/register', $this->registration(['temp_uuid' => $uuid]))->assertRedirect();
        $this->assertCount(0, Customer::find(auth()->user()->customer_id)->getMedia('attachments'));
    }

    public function test_register_keeps_the_visitors_own_licence(): void
    {
        $this->get(route('register'));
        $uuid = $this->upload()->assertOk()->json('uuid');

        $this->post('/register', $this->registration(['temp_uuid' => $uuid]))->assertRedirect();

        $this->assertCount(1, Customer::find(auth()->user()->customer_id)->getMedia('attachments'));
        $this->assertNull(TemporaryUpload::where('uuid', $uuid)->first());
    }

    // ---------------------------------------------------------------- login

    public function test_active_customer_can_log_in(): void
    {
        $user = $this->customerUser();
        $this->post('/login', ['username' => $user->username, 'password' => self::PASSWORD, '_token' => csrf_token()]);
        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_customer_cannot_log_in(): void
    {
        $user = $this->customerUser(['is_active' => 0]);
        $this->post('/login', ['username' => $user->username, 'password' => self::PASSWORD, '_token' => csrf_token()])
            ->assertSessionHasErrors(['username' => 'Your account is inactive. Please contact us.']);
        $this->assertGuest();
    }

    public function test_staff_account_cannot_log_in(): void
    {
        $staff = new User();
        $staff->forceFill([
            'name' => 'ZZ Staff', 'username' => 'zzstaff' . uniqid(), 'email' => 'zzstaff' . uniqid() . '@example.com',
            'password' => Hash::make(self::PASSWORD), 'role_id' => Bouncer::role()->where('name', 'superadmin')->value('id'), 'is_active' => 1,
        ])->save();

        $this->post('/login', ['username' => $staff->username, 'password' => self::PASSWORD, '_token' => csrf_token()])
            ->assertSessionHasErrors(['username' => "This account can't be used on the booking website."]);
        $this->assertGuest();
    }

    public function test_wrong_password_does_not_reveal_the_account_type(): void
    {
        $user = $this->customerUser(['is_active' => 0]);
        $this->post('/login', ['username' => $user->username, 'password' => 'wrong', '_token' => csrf_token()])
            ->assertSessionHasErrors(['username' => 'Invalid username or password.']);
    }

    // ---------------------------------------------------------------- customers added by staff

    public function test_customer_added_by_staff_signs_up_on_their_own_record(): void
    {
        // Added in rngcarrental: local phone format, IC with dashes, no login
        $walkIn = Customer::create([
            'first_name' => 'Walk', 'last_name' => 'In', 'email' => 'zzwalkin' . uniqid() . '@example.com',
            'phone_number' => '012-345 6789', 'ic' => '900101-13-5555',
        ]);

        $this->post('/register', $this->registration(['email' => $walkIn->email, 'phone_number' => '+60 12-345 6789', 'ic' => '900101135555']))
            ->assertSessionHasNoErrors();

        $this->assertSame($walkIn->id, auth()->user()->customer_id, 'linked to the existing customer');
        $this->assertSame(1, Customer::where('email', $walkIn->email)->count(), 'no duplicate customer');
        $this->assertSame('+60123456789', $walkIn->fresh()->phone_number);
    }

    public function test_email_alone_does_not_take_over_a_customer(): void
    {
        $walkIn = Customer::create([
            'first_name' => 'Walk', 'last_name' => 'In', 'email' => 'zzwalkin' . uniqid() . '@example.com',
            'phone_number' => '0123456789', 'ic' => '900101-13-5555',
        ]);

        $this->post('/register', $this->registration(['email' => $walkIn->email, 'ic' => '850505-13-1111']))->assertSessionHasNoErrors();

        $this->assertNotSame($walkIn->id, auth()->user()->customer_id, 'a different IC gets a new customer');
        $this->assertSame('Walk', $walkIn->fresh()->first_name, 'the existing customer is untouched');
    }

    public function test_email_of_an_existing_login_is_still_taken(): void
    {
        $existing = $this->customerUser();
        $this->post('/register', $this->registration(['email' => $existing->email]))
            ->assertSessionHasErrors(['email' => 'This email address is already registered.']);
        $this->assertGuest();
    }

    // ---------------------------------------------------------------- removed leftover pages

    public function test_leftover_pages_are_gone(): void
    {
        $this->get('/test')->assertNotFound();
        $this->post('/quotation/download', ['_token' => csrf_token()])->assertNotFound();
    }
}
