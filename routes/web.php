<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// ROUTE PENPOS
Route::group(
    ['prefix' => 'penpos', 'as' =>'penpos.', 'middleware' => 'penpos'],
    function () {
        // Route::get('/dashboard', 'Peserta\DashboardController@index')->name('dashboard');
    }
);

// ROUTE PESERTA
Route::group(
    ['prefix' => 'peserta', 'as' => 'peserta.', 'middleware' => 'peserta'],
    function () {
        // Dashboard --> Inventory (Acara)
        Route::get('/dashboard', 'Peserta\DashboardController@index')->name('dashboard');


        // Inventory --> Gudang (Acara)
    }
);