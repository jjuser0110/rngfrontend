<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('/cars')->as('cars.')->group(function() {
    Route::get('/index', 'CarController@index')->name('index');
    Route::get('/create', 'CarController@create')->name('create');
    Route::post('/store', 'CarController@store')->name('store');
    Route::get('/edit/{car}', 'CarController@edit')->name('edit');
    Route::post('/update/{car}', 'CarController@update')->name('update');
    Route::get('/destroy/{car}', 'CarController@destroy')->name('destroy');
    Route::get('/cart', 'CarController@cart')->name('cart');
    Route::get('/fillinfo', 'CarController@fillinfo')->name('fillinfo');
    Route::get('/finalreview', 'CarController@finalreview')->name('finalreview');
});
