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

Route::prefix('astro')->group(function() {
    Route::get('/', 'AstroController@index');

    Route::get('/form', function () {
        return view('astro::form');})->name('astro.form');
    Route::post('/form', 'AstroController@index');
});
