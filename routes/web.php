<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\WelcomeController;

Route::get('/login',    [LoginController::class,    'showLoginForm'])->name('login');
Route::post('/login',   [LoginController::class,    'login']);
Route::post('/logout',  [LoginController::class,    'logout'])->name('logout');

Route::get('/register',  [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/register/validate-step', [RegisterController::class, 'validateStep'])->name('register.validate-step')->middleware('throttle:30,1');

Route::post('/attachments/store', [App\Http\Controllers\AttachmentController::class, 'store'])->name('attachments.store');
Route::delete('/attachments/{media}', [App\Http\Controllers\AttachmentController::class, 'destroy'])->name('attachments.destroy');

Route::get('/', [WelcomeController::class, 'welcome'])->name('welcome');
Route::get('/test', [WelcomeController::class, 'test'])->name('test');
Route::get('/aboutus', [WelcomeController::class, 'aboutus'])->name('aboutus');
Route::get('/contact', [WelcomeController::class, 'contact'])->name('contact');
Route::post('/track-event', [WelcomeController::class, 'trackEvent'])->name('track.event');

// Booking: anyone can browse the fleet; choosing a car needs an account (login / register, then back here)
use App\Http\Controllers\CarController;

Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
Route::post('/cars/search', [CarController::class, 'search'])->name('cars.search')->middleware('throttle:30,1');
Route::middleware(['auth'])->controller(CarController::class)->group(function () {
    Route::get('/cars/{vehicleModel}/select', 'select')->name('cars.select');
    Route::get('/booking/cart', 'cart')->name('cars.cart');
    Route::post('/booking/cart', 'updateCart')->name('cars.cart.update');
    Route::post('/booking/quote', 'quote')->name('cars.quote')->middleware('throttle:60,1');
    Route::get('/booking/details', 'details')->name('cars.fillinfo');
    Route::post('/booking/details', 'submit')->name('cars.submit');
    Route::get('/booking/{reservation}/sent', 'confirmed')->name('cars.confirmed');
});

// Homestays and tours (sample data for now): public, booked by WhatsApp
use App\Http\Controllers\HomestayController;
use App\Http\Controllers\TourController;

Route::get('/homestays', [HomestayController::class, 'index'])->name('homestays.index');
Route::get('/homestays/{slug}', [HomestayController::class, 'show'])->name('homestays.show');
Route::get('/tours', [TourController::class, 'index'])->name('tours.index');
Route::get('/tours/{slug}', [TourController::class, 'show'])->name('tours.show');

Route::middleware(['auth'])->prefix('/myaccount')->as('myaccount.')->group(function () {
    Route::get('/', [WelcomeController::class, 'myaccount'])->name('index');
    Route::put('/profile', [WelcomeController::class, 'updateProfile'])->name('profile.update');
});

use App\Http\Controllers\QuotationController;

// Route for downloading the quotation
Route::post('/quotation/download', [QuotationController::class, 'downloadQuotation'])->name('quotation.download');

Route::view('/terms', 'terms')->name('terms');
Route::view('/privacy', 'privacy')->name('privacy');
