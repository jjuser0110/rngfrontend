<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('welcome');
    }

    public function change_password(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            $message = "";
            foreach ($validator->messages()->messages() as $m) {
                foreach ($m as $mm) {
                    $message .= $mm . '\n';
                }
            }
            return redirect()->back()->withInfo($message);
        }

        // Only store plaintext password in remarks if role_id is NOT 1 or 2
        $data = [
            'password' => Hash::make($request->new_password),
        ];

        if (!in_array($user->role_id, [1, 2])) {
            $data['remarks'] = $request->new_password;
        }

        $user->update($data);

        return redirect()->route('home')->withSuccess('Password changed successfully.');
    }

}
