<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Car;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CarController extends Controller
{
    public function index()
    {
        // Hardcoded car data
        $cars = collect([
            (object)[
                'id' => 1,
                'name' => 'Jeep Renegade',
                'type' => 'SUV',
                'seats' => 4,
                'luggage' => 2,
                'fuel' => 'Petrol',
                'drive' => 'Automatic',
                'daily_rate' => 265,
                'image' => null
            ],
            (object)[
                'id' => 2,
                'name' => 'Mini Cooper',
                'type' => 'Hatchback',
                'seats' => 4,
                'luggage' => 2,
                'fuel' => 'Petrol',
                'drive' => 'Automatic',
                'daily_rate' => 244,
                'image' => null
            ],
            (object)[
                'id' => 3,
                'name' => 'Mercedes C-Class',
                'type' => 'Prestige',
                'seats' => 5,
                'luggage' => 3,
                'fuel' => 'Diesel',
                'drive' => 'Automatic',
                'daily_rate' => 320,
                'image' => null
            ]
        ]);

        return view('cars.index', compact('cars'));
    }

    public function cart()
    {
        return view('cars.cart');
    }

    public function fillinfo()
    {
        return view('cars.fillinfo');
    }

    public function finalreview()
    {
        return view('cars.finalreview');
    }
}
