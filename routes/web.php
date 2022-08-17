<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index')->name('home');

Auth::routes();
Route::post('/map/update-map', 'Penpos\MapController@updateMap')->name('map.update');

Route::post('/update-season/now', 'Penpos\DashboardController@updateNow')->name('update.now');

// ROUTE PENPOS
Route::group(
    ['prefix' => 'penpos', 'as' => 'penpos.', 'middleware' => 'penpos'],
    function () {
        // Dashboard
        Route::get('/', 'Penpos\DashboardController@index')->name('index');

        // Dashboard Peserta
        Route::get('/dashboard/peserta/{team}', 'Penpos\DashboardController@getDataTeam')->name('dashboard.peserta.data');

        // Fitur Map
        Route::get('/map', 'Penpos\MapController@index')->name('map');
        Route::post('/map/move', 'Penpos\MapController@move')->name('map.move');
        Route::post('/map/undo', 'Penpos\MapController@undo')->name('map.undo');
        Route::post('/map/spawn', 'Penpos\MapController@spawn')->name('map.spawn');
        Route::post('/map/action', 'Penpos\MapController@action')->name('map.action');
        Route::post('/map/action/buy/transport', 'Penpos\MapController@buyTransport')->name('map.buy.transport');
        Route::post('/map/action/buy/ingridient', 'Penpos\MapController@buyIngridient')->name('map.buy.ingridient');
        Route::post('/map/action/buy/machine', 'Penpos\MapController@buyMachine')->name('map.buy.machine');
        Route::post('/map/action/buy/service', 'Penpos\MapController@buyService')->name('map.buy.service');

        Route::post('/map/get/capacity', 'Penpos\MapController@getCapacity')->name('map.get.capacity');
        Route::post('/map/back/to/company', 'Penpos\MapController@backToCompany')->name('map.back.to.company');

        // Investasi
        Route::get('/investasi', 'Penpos\InvestasiController@index')->name('investasi');
        Route::post('/investasi/save', 'Penpos\InvestasiController@save')->name('investasi.save');

        // Inventory
        Route::get('/inventory/{team}', 'Penpos\InventoryController@index')->name('inventory');
        Route::post('/inventory/ingridient-expired', 'Penpos\InventoryController@ingridientExpired')->name('inventory.expired');

        // Maintenance
        Route::get('/maintenance', 'Penpos\MaintenanceController@index')->name('maintenance');
        Route::post('/maintenance/get-team-machines', 'Penpos\MaintenanceController@getTeamMachine')->name('maintenance.get.machine');
        Route::post('/maintenance/save', 'Penpos\MaintenanceController@save')->name('maintenance.save');

        // Update Season
        Route::get('/update-season', 'Penpos\DashboardController@updateSeason')->name('update.season');

        // LeaderBoard
        Route::get('/leader-board', 'Penpos\LeaderboardController@index')->name('leaderboard');
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
        Route::post('/inventory/ingridient-expired', 'Peserta\InventoryController@ingridientExpired')->name('inventory.expired');

        // Deskripsi Mesin --> 
        Route::get('/deskripsi', 'Peserta\DescriptionController@index')->name('deskripsi'); // -> /peserta/deskripsi

        // Mesin --> 
        Route::get('/mesin', 'Peserta\MesinController@index')->name('mesin'); // -> /peserta/mesin
        Route::post('/mespin/mesin-available', 'Peserta\MesinController@getAvailableMachine')->name('mesin.get');
        Route::post('/mesin/set-mesin', 'Peserta\MesinController@setMachine')->name('mesin.set');
        Route::post('/mesin/save-mesin', 'Peserta\MesinController@saveMachine')->name('mesin.save');
        Route::post('/mesin/jual-mesin', 'Peserta\MesinController@sellMachine')->name('mesin.jual');
        Route::post('/mesin/reset-mesin', 'Peserta\MesinController@resetMachine')->name('mesin.reset');
        Route::post('/mesin/save-tambahan-mesin', 'Peserta\MesinController@saveMachineTambahan')->name('mesin.save.tambahan');

        // produksi -->
        Route::get('/produksi', 'Peserta\ProduksiController@index')->name('produksi'); // -> /peserta/produksi
        Route::post('/produksi/product', 'Peserta\ProduksiController@production')->name('produksi.produk');

        // Marketing
        Route::get('/marketing', 'Peserta\MarketingController@index')->name('marketing');
        Route::post('/marketing/sell-items', 'Peserta\MarketingController@sell')->name('marketing.sell');
        Route::post('/marketing/cooldown', 'Peserta\MarketingController@cooldown')->name('marketing.cooldown');
        Route::post('/marketing/null/cooldown', 'Peserta\MarketingController@nullCooldown')->name('marketing.null.cooldown');

        // Investasi
        Route::get('/investasi', 'Peserta\InvestasiController@index')->name('investasi');
        Route::get('/investasi/{investation}/{question:nomor}', 'Peserta\InvestasiController@show')->name('investasi.show');
        Route::post('/investasi/submit', 'Peserta\InvestasiController@submission')->name('investasi.submit');

        // Leaderboard Pemain
        Route::get('/leaderboard', 'Peserta\LeaderboardPemainController@index')->name('leaderboard');

        // Level
        Route::get('/level', 'Peserta\LevelController@index')->name('level');
        Route::post('/level/update-syarat', 'Peserta\LevelController@updateSyarat')->name('level.update');
        Route::post('/level/upgrade-level', 'Peserta\LevelController@upgradeLevel')->name('level.upgrade');
    }
);
