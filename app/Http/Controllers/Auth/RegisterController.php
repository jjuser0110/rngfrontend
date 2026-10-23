<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\User;
use App\Models\Country;
use App\Models\TemporaryUpload;
use App\Rules\PhoneNumber;
use Illuminate\Support\Facades\DB;
use Bouncer;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $countries = Country::all();
        return view('auth.register', compact('countries'));
    }

    /**
     * Single source of truth for the wizard's validation rules.
     * Used by validateStep() (on every "Next" click) and by register() (final submit).
     */
    private function rulesForStep(int $step): array
    {
        $rules = [
            1 => [
                'username'              => 'required|string|max:255|unique:users,username|unique:customers,username',
                'email'                 => 'required|string|email|max:255|unique:customers,email|unique:users,email',
                'password'              => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required',
            ],
            2 => [
                'first_name'     => 'required|string|max:255',
                'last_name'      => 'required|string|max:255',
                // Full international number from the country-code picker, e.g. +60123456789
                'phone_number'   => ['required', 'string', 'max:20', new PhoneNumber],
                'date_of_birth'  => 'required|date|before:today',
                'address_line_1' => 'nullable|string|max:255',
                'address_line_2' => 'nullable|string|max:255',
                'city'           => 'nullable|string|max:255',
                'state'          => 'nullable|string|max:255',
                'postcode'       => 'nullable|string|max:20',
                'country'        => 'nullable|string|max:255',
            ],
            3 => [
                'ic'              => 'required|string|max:255',
                'expiration_date' => 'required|date',
                'attachments.*'   => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            ],
        ];

        return $rules[$step] ?? [];
    }

    private function validationMessages(): array
    {
        return [
            'username.required'              => 'Username is required.',
            'username.unique'                => 'This username is already taken.',
            'email.required'                 => 'Email address is required.',
            'email.email'                    => 'Please enter a valid email format.',
            'email.unique'                   => 'This email address is already registered.',
            'password.required'              => 'Password is required.',
            'password.min'                   => 'Password must be at least :min characters.',
            'password.confirmed'             => 'Passwords do not match.',
            'password_confirmation.required' => 'Please confirm your password.',
            'first_name.required'            => 'First name is required.',
            'last_name.required'             => 'Last name is required.',
            'phone_number.required'          => 'Phone number is required.',
            'date_of_birth.required'         => 'Date of birth is required.',
            'date_of_birth.before'           => 'Date of birth must be in the past.',
            'ic.required'                    => 'IC / Passport number is required.',
            'expiration_date.required'       => 'Expiration date is required.',
        ];
    }

    /**
     * AJAX endpoint called when the user clicks "Next" in the wizard.
     * Returns 200 {valid: true} or the standard Laravel 422 JSON with per-field errors.
     */
    public function validateStep(Request $request)
    {
        $step = (int) $request->input('step');

        abort_unless(in_array($step, [1, 2, 3], true), 422, 'Invalid step.');

        $request->validate($this->rulesForStep($step), $this->validationMessages());

        return response()->json(['valid' => true]);
    }

    public function register(Request $request)
    {
        // Re-validate every step on the final submit (never trust the client-side wizard)
        $validated = $request->validate(
            array_merge(
                $this->rulesForStep(1),
                $this->rulesForStep(2),
                $this->rulesForStep(3)
            ),
            $this->validationMessages()
        );

        // Only validated fields reach the database (no created_by, company links, etc. from the request)
        $data = collect($validated)->except(['password_confirmation', 'attachments'])->all();
        $data['password'] = Hash::make($request->password);
        $data['phone_number'] = PhoneNumber::normalize($data['phone_number']);

        $user = DB::transaction(function () use ($data, $request) {
            $customer = Customer::create($data);

            if ($request->temp_uuid) {
                $temp = TemporaryUpload::where('uuid', $request->temp_uuid)->first();
                if ($temp) {
                    foreach ($temp->getMedia('attachments') as $media) {
                        $media->move($customer, 'attachments');
                    }
                    $temp->delete();
                }
            }

            // Created by rngcarrental's add_customer_role_to_roles_table migration
            $role = Bouncer::role()->where('name', 'customer')->first();
            if (!$role) {
                throw new \RuntimeException('The "customer" role is missing. Run the rngcarrental migrations.');
            }

            $user = new User();
            $user->name        = $customer->full_name;
            $user->username    = $customer->username;
            $user->email       = $customer->email;
            $user->password    = $customer->password;
            $user->customer_id = $customer->id;
            $user->role_id     = $role->id;
            $user->is_active   = 1;
            $user->save();

            $user->assign($role);

            return $user;
        });

        Auth::login($user);

        // Back to where the customer was heading (e.g. the cart after choosing a car)
        return redirect()->intended(route('myaccount.index'));
    }
}
