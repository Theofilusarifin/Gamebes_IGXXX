<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();
Route::post('/map/update-map', 'Penpos\MapController@updateMap')->name('map.update');

// ROUTE PENPOS
Route::group(
    ['prefix' => 'penpos', 'as' => 'penpos.', 'middleware' => 'penpos'],
    function () {
        // Dashboard
        Route::get('/', 'Penpos\DashboardController@index')->name('index');

        // Fitur Map
        Route::get('/map', 'Penpos\MapController@index')->name('map');
        Route::post('/map/move', 'Penpos\MapController@move')->name('map.move');
        Route::post('/map/spawn', 'Penpos\MapController@spawn')->name('map.spawn');
        Route::post('/map/action', 'Penpos\MapController@action')->name('map.action');
        Route::post('/map/action/buy-items', 'Penpos\MapController@buy')->name('map.buy');

        // Marketing
        Route::get('/marketing', 'Penpos\MarketingController@index')->name('marketing');
        Route::post('/marketing/sell-items', 'Penpos\MarketingController@sell')->name('marketing.sell');

        // Investasi
        Route::get('/investasi', 'Penpos\InvestasiController@index')->name('investasi');
        Route::post('/investasi/save', 'Penpos\InvestasiController@save')->name('investasi.save');

        // Update Season
        Route::get('/update-season', 'Penpos\DashboardController@updateSeason')->name('update.season');
        Route::post('/update-season/now', 'Penpos\DashboardController@updateNow')->name('update.now');

        // Maintenance
        Route::get('/maintenance', 'Penpos\MaintenanceController@index')->name('maintenance');
        Route::post('/maintenance/get-team-machines', 'Penpos\MaintenanceController@getTeamMachine')->name('maintenance.get.machine');
        Route::post('/maintenance/save', 'Penpos\MaintenanceController@save')->name('maintenance.save');

    }
);

// ROUTE PESERTA
Route::group(
    ['prefix' => 'peserta', 'as' => 'peserta.', 'middleware' => 'peserta'],
    function () {
        // Dashboard --> Inventory (Acara)
        Route::get('/', 'Peserta\DashboardController@index')->name('index'); // -> /peserta/
        
        // List Harga -->
        Route::get('/harga', 'Peserta\ListHargaController@index')->name('harga'); // -> /peserta/harga
        
        // Inventory --> Gudang (Acara)
        Route::get('/inventory', 'Peserta\InventoryController@index')->name('inventory'); // -> /peserta/inventory
        
        // Mesin --> 
        Route::get('/mesin', 'Peserta\MesinController@index')->name('mesin'); // -> /peserta/mesin
        Route::post('/mespin/mesin-available', 'Peserta\MesinController@getAvailableMachine')->name('mesin.get');
        Route::post('/mesin/set-mesin', 'Peserta\MesinController@setMachine')->name('mesin.set');
        Route::post('/mesin/save-mesin', 'Peserta\MesinController@saveMachine')->name('mesin.save');
        Route::post('/mesin/jual-mesin', 'Peserta\MesinController@sellMachine')->name('mesin.jual');

        // produksi -->
        Route::get('/produksi', 'Peserta\ProduksiController@index')->name('produksi'); // -> /peserta/produksi
        Route::post('/produksi/product', 'Peserta\ProduksiController@production')->name('produksi.produk');

        // Investasi
        Route::get('/investasi', 'Peserta\InvestasiController@index')->name('investasi');
        Route::get('/investasi/{investation}/{question:nomor}', 'Peserta\InvestasiController@show')->name('investasi.show');
        Route::post('/investasi/submit', 'Peserta\InvestasiController@submission')->name('investasi.submit');


    }
);
