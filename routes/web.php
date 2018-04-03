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
Route::resource('shippers','Shippers\shipperLoginController');
Route::get('/admins',function(){
	return view('admin/index');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/ecommerce', 'Shippers\ShipperDashboardController@ecommerce');

//Route::get('/shipperDashboard', 'Shippers\ShipperDashboardController@index');
Route::prefix('shipper')->group(function () {
    Route::get('/dashboard', 'Shippers\ShipperDashboardController@index');
    Route::get('/order/management', 'Shippers\ShipperDashboardController@orderList');
    Route::get('/order/pending', 'Shippers\ShipperDashboardController@orderPending');

});
