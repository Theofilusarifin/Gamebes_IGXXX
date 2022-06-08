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
        Route::get('/', 'Penpos\DashboardController@index')->name('index');

        // Route::get('/', 'Penpos\DashboardController@index')->name('index');

    }
);

// ROUTE PESERTA
Route::group(
    ['prefix' => 'peserta', 'as' => 'peserta.', 'middleware' => 'peserta'],
    function () {
        // Dashboard --> Inventory (Acara)
        Route::get('/', 'Peserta\DashboardController@index')->name('index');


        // Inventory --> Gudang (Acara)
    }
);