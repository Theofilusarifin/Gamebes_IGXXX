<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});

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

Route::resource('', '');

Route::resource('', '');

Route::resource('', '');

Route::resource('', '');

Route::resource('', '');

Route::resource('', '');

Route::resource('', '');

Route::resource('', '');

Route::resource('', '');

Route::resource('', '');

Route::resource('', '');

Route::resource('', '');

Route::resource('', '');

Route::resource('', '');

Route::resource('', '');