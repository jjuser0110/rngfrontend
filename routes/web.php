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

Route::post('/attachments/store', [App\Http\Controllers\AttachmentController::class, 'store'])->name('attachments.store')->middleware('throttle:20,1');
Route::delete('/attachments/{media}', [App\Http\Controllers\AttachmentController::class, 'destroy'])->name('attachments.destroy')->middleware('throttle:20,1');

Route::get('/', [WelcomeController::class, 'welcome'])->name('welcome');
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

// Security deposit paid online with DOKU (off until DOKU_ENABLED and the keys are set)
use App\Http\Controllers\PaymentController;

Route::middleware(['auth'])->group(function () {
    Route::post('/bookings/{reservation}/pay-deposit', [PaymentController::class, 'pay'])->name('payments.doku.pay')->middleware('throttle:10,1');
    Route::get('/bookings/{reservation}/payment/{transaction}', [PaymentController::class, 'result'])->name('payments.doku.result');
});
// DOKU's server calls this; no login or CSRF token (see VerifyCsrfToken), checked by DOKU's signature instead
Route::post('/payments/doku/notify', [PaymentController::class, 'notify'])->name('payments.doku.notify')->middleware('throttle:120,1');
// LOCAL TESTING ONLY: fake DOKU payment page (404 unless DOKU_FAKE=true and APP_ENV=local)
Route::get('/payments/fake-doku/{invoice}', [App\Http\Controllers\FakeDokuController::class, 'show'])->name('payments.fake-doku.show');
Route::post('/payments/fake-doku/{invoice}', [App\Http\Controllers\FakeDokuController::class, 'act'])->name('payments.fake-doku.act');

Route::middleware(['auth'])->prefix('/myaccount')->as('myaccount.')->group(function () {
    Route::get('/', [WelcomeController::class, 'myaccount'])->name('index');
    Route::put('/profile', [WelcomeController::class, 'updateProfile'])->name('profile.update');
    Route::get('/bookings/{reservation}/invoice', [WelcomeController::class, 'invoice'])->name('invoice');
    Route::delete('/bookings/{reservation}', [WelcomeController::class, 'removeUnpaid'])->name('bookings.remove');
});

Route::view('/terms', 'terms')->name('terms');
Route::view('/privacy', 'privacy')->name('privacy');
