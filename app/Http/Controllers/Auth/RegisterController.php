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

    public function register(Request $request)
    {
        $request->validate([
            'email'             => 'required|string|email|max:255|unique:customers,email|unique:users,email',
            'password'          => 'required|string|min:6|confirmed',
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'phone_number'      => 'required|string|max:20',
            'date_of_birth'     => 'required|date',
            'ic'                => 'required|string|max:255',
            'expiration_date'   => 'required|date',
            'attachments.*'     => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $data = $request->except(['password_confirmation', 'temp_uuid']);
        $data['password'] = Hash::make($request->password);
        $data['is_agent'] = 0;

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

        $user = new User();
        $user->name        = $customer->first_name . ' ' . $customer->last_name;
        $user->username    = $customer->email;
        $user->email       = $customer->email;
        $user->password    = Hash::make($request->password);
        $user->customer_id = $customer->id;
        $user->role_id     = 5;
        $user->is_active   = 1;
        $user->save();

        $role = Bouncer::role()->find(5);
        if ($role) {
            $user->assign($role);
        }

        if ($request->filled('company_ids')) {
            $user->companies()->sync($request->company_ids);
        }

        Auth::guard('customer')->login($customer);

        return redirect()->route('myaccount');
    }
}
