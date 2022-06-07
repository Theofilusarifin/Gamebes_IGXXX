<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Auth::routes();

// ROUTE PENPOS
Route::group(
    ['prefix' => 'penpos', 'as' => 'penpos.'],
    function () {
    }
);

// ROUTE PESERTA
Route::group(
    ['prefix' => 'peserta', 'as' => 'peserta.'],
    function () {
        // Dashboard --> Inventory (Acara)
        Route::get('/dashboard', 'Peserta\DashboardController@index')->name('dashboard');


        // Inventory --> Gudang (Acara)
    }
);