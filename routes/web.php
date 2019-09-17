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

Route::post('/sales/add','SalesController@addSale')->name('add.sale');
Route::post('/sales/delete','SalesController@deleteSale')->name('delete.sale');

Route::post('/products/add','ProductsController@addProduct')->name('add.product');
Route::post('/products/delete','ProductsController@deleteProduct')->name('delete.product');
Route::post('/products/update','ProductsController@updateProduct')->name('update.product');

Route::post('/managers/add','ClientsController@addManager')->name('add.manager');
Route::post('/managers/update','ClientsController@updateManager')->name('update.manager');
Route::post('/managers/delete','ClientsController@deleteManager')->name('delete.manager');

// excel

Route::get('/admin/excel/actions', 'AdminsController@showExcelActionPage')->name('admin.excel');
Route::get('/admin/export/products', 'ProductsController@export')->name('admin.products.export');
Route::post('/admin/import/products', 'ProductsController@import')->name('admin.products.import');

// api
Route::get('api/get-products','ProductsController@getProducts')->name('get.products');
Route::get('api/get-managers','ClientsController@getManagers')->name('get.products');
Route::get('api/get-current-client','ClientsController@getCurrentClient')->name('get.current.client');
Route::get('api/get-sales-list','SalesController@getSalesList')->name('get.sales.list');
Route::get('api/products/{currency}','ProductsController@productsByCurrency')->name('products.eur.prices');
