<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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
        return view('myaccount');
    }

    public function contact()
    {
        return view('contact');
    }

}