<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/login',    [LoginController::class,    'showLoginForm'])->name('login');
Route::post('/login',   [LoginController::class,    'login']);
Route::post('/logout',  [LoginController::class,    'logout'])->name('logout');

Route::get('/register',  [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/', [App\Http\Controllers\WelcomeController::class, 'welcome'])->name('welcome');
Route::get('/aboutus', [App\Http\Controllers\WelcomeController::class, 'aboutus'])->name('aboutus');
Route::get('/contact', [App\Http\Controllers\WelcomeController::class, 'contact'])->name('contact');
Route::post('/track-event', [App\Http\Controllers\WelcomeController::class, 'trackEvent'])->name('track.event');

Route::group(['middleware' => ['auth:customer']], function () {
    Route::get('/myaccount', [App\Http\Controllers\WelcomeController::class, 'myaccount'])->name('myaccount');
});

use App\Http\Controllers\QuotationController;

// Route for downloading the quotation
Route::post('/quotation/download', [QuotationController::class, 'downloadQuotation'])->name('quotation.download');
