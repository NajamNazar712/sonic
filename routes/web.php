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
//Route::resource('shippers','Shippers\shipperLoginController');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/ecommerce', 'Shippers\ShipperDashboardController@ecommerce');

//Route::get('/shipperDashboard', 'Shippers\ShipperDashboardController@index');
Route::prefix('cod')->group(function () {
    Route::get('/login','Auth\LoginController@showLoginForm')->name('cod.login');
    Route::post('/login','Auth\LoginController@login')->name('cod.login.submit');
    Route::get('/register','Auth\RegisterController@showRegistrationForm')->name('cod.register');
    Route::post('/register','Auth\RegisterController@register')->name('cod.register.submit');
    Route::get('/dashboard', 'Shippers\ShipperDashboardController@index')->name('cod.dashboard');
    Route::get('/order/management', 'Shippers\ShipperDashboardController@orderList');
    Route::get('/order/pending', 'Shippers\ShipperDashboardController@orderPending');
    Route::get('/logout','Auth\LoginController@logout')->name('cod.logout');

    Route::post('/logout','Auth\LoginController@logout')->name('cod.logout');

});
Route::prefix('admin')->group(function () {
    Route::get('/login','Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login','Auth\AdminLoginController@login')->name('admin.login.submit');
    Route::get('/dashboard', 'Admins\AdminDashboardController@index')->name('admin.dashboard');
    Route::get('/order/management', 'Admins\AdminDashboardController@orderList');
    Route::get('/order/pending', 'Admins\AdminDashboardController@orderPending');
    Route::get('/logout','Auth\AdminLoginController@logout')->name('admin.logout');
    Route::post('/logout','Auth\AdminLoginController@logout')->name('admin.logout');

});
