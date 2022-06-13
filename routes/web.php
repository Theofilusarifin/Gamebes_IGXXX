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
        Route::post('/map/move', 'Penpos\MapController@move')->name('map.move');
        Route::post('/map/spawn', 'Penpos\MapController@spawn')->name('map.spawn');
        Route::post('/map/action', 'Penpos\MapController@action')->name('map.action');
        Route::post('/map/action/buy-items', 'Penpos\MapController@buy')->name('map.buy');

        
        
        // Fitur Susun Mesin
        Route::get('/mesin', 'Penpos\DashboardController@index')->name('mesin');
    }
);

Route::post('/map/update-map', 'Penpos\MapController@updateMap')->name('map.update');

// ROUTE PESERTA
Route::group(
    ['prefix' => 'peserta', 'as' => 'peserta.', 'middleware' => 'peserta'],
    function () {
        // Dashboard --> Inventory (Acara)
        Route::get('/', 'Peserta\DashboardController@index')->name('index'); // -> /peserta/
        // Inventory --> Gudang (Acara)
        Route::get('/inventory', 'Peserta\InventoryController@index')->name('inventory'); // -> /peserta/inventory
        // Mesin --> 
        Route::get('/mesin', 'Peserta\MesinController@index')->name('mesin'); // -> /peserta/mesin
    }
);