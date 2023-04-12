<?php

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

use Modules\Astro\Http\Controllers\AstroController;

Route::prefix('lifeexpert')->group(function() {
    Route::get('/', 'LifeExpertController@index');
    Route::get('/form', function () {
        return view('lifeexpert::form');
    })->name('life.form');
    Route::post('/form', 'LifeExpertController@index');
});
