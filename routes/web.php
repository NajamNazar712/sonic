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
Route::resource('shippers','shipperLoginController');
Route::get('/admins',function(){
	return view('admin/index');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/shipperDashboard', 'ShipperDashboardController@index');
Route::get('/ecommerce', 'ShipperDashboardController@ecommerce');
