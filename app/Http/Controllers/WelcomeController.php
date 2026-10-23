<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Models\TemporaryUpload;
use App\Rules\PhoneNumber;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class WelcomeController extends Controller
{
    public function welcome()
    {
        return view('welcome');
    }

    public function aboutus()
    {
        return view('aboutus');
    }

    public function myaccount()
    {
        $user = Auth::user();

        $customer = Customer::where('id', $user->customer_id)->first();

        return view('myaccount', compact('customer'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $customer = Customer::where('id', $user->customer_id)->first();

        if (!$customer) {
            return back()->withErrors(['email' => 'Associated customer record not found.']);
        }

        // Validate incoming profile data, password changes, and license renewals
        $request->validate([
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'phone_number'     => ['required', 'string', 'max:20', new PhoneNumber],
            'date_of_birth'    => 'required|date',
            'ic'               => 'required|string|max:255',
            'expiration_date'  => 'required|date',
            'address_line_1'   => 'nullable|string|max:255',
            'address_line_2'   => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:255',
            'state'            => 'nullable|string|max:255',
            'postcode'         => 'nullable|string|max:20',

            // License document validation rules
            'license_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',

            // Password validation rules
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password'     => 'nullable|string|min:6|confirmed',
        ]);

        // Update customer personal and address details
        $customer->first_name      = $request->first_name;
        $customer->last_name       = $request->last_name;
        $customer->phone_number    = PhoneNumber::normalize($request->phone_number);
        $customer->date_of_birth   = $request->date_of_birth;
        $customer->ic              = $request->ic;
        $customer->expiration_date = $request->expiration_date;
        $customer->address_line_1  = $request->address_line_1;
        $customer->address_line_2  = $request->address_line_2;
        $customer->city            = $request->city;
        $customer->state           = $request->state;
        $customer->postcode        = $request->postcode;

        if ($request->filled('temp_uuid')) {
            $temp = TemporaryUpload::where('uuid', $request->temp_uuid)->first();
            if ($temp) {
                // Clear existing attachments if replacing/updating
                $customer->clearMediaCollection('attachments');

                foreach ($temp->getMedia('attachments') as $media) {
                    $media->move($customer, 'attachments');
                }
                $temp->delete();
            }
        }

        // Handle Password Update with cross-table synchronization
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The provided current password does not match our records.'])->withInput();
            }

            $hashedPassword = Hash::make($request->new_password);

            $user->password = $hashedPassword;
            $user->save();

            $customer->password = $hashedPassword;
        }

        $customer->save();

        $user->name = $customer->first_name . ' ' . $customer->last_name;
        $user->save();

        return redirect()->route('myaccount.index')->with('success', 'Your profile and license document have been successfully updated.');
    }

    public function contact()
    {
        return view('contact');
    }
}
