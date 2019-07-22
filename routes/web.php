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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/dashboard', 'DashboardController@showDashboard')
    ->middleware('auth')
    ->name('dashboard.index');

Route::get('/admin','AdminsController@index')
    ->middleware('auth')
    ->name('admin.dashboard');

Route::get('/home','HomeController@index')
    ->middleware('auth')
    ->name('home');

// api

Route::get('api/get-products','ProductsController@getProducts')->name('get.products');