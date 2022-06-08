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

        // Fitur Map
        Route::get('/map', 'Penpos\MapController@index')->name('map');

        // Fitur Susun Mesin
        Route::get('/mesin', 'Penpos\DashboardController@index')->name('mesin');
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