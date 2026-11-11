<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\CarController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::validate($request->only('username', 'password'))) {
            return back()->withErrors(['username' => 'Invalid username or password.'])->withInput();
        }

        // Only active customer accounts sign in here (staff use rngcarrental, which blocks customers the same way)
        $user = Auth::getLastAttempted();
        if (!$user->customer_id) {
            return back()->withErrors(['username' => "This account can't be used on the booking website."])->withInput();
        }
        if ($user->is_active != 1) {
            return back()->withErrors(['username' => 'Your account is inactive. Please contact us.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(CarController::afterLoginUrl());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
