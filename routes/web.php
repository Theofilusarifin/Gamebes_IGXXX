<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

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



Route::resource('user', 'UserController');

Route::resource('admin', 'AdminController');

Route::resource('answer', 'AnswerController');

Route::resource('ingridient', 'IngridientController');

Route::resource('ingridient_season', 'IngridientSeasonController');

Route::resource('ingridient_store', 'IngridientStoreController');

Route::resource('investation', 'InvestationController');

Route::resource('investation_team', 'InvestationTeamController');

Route::resource('machine', 'MachineController');

Route::resource('machine_store', 'MachineStoreController');

Route::resource('product', 'ProductController');
