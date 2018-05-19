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

Route::prefix('cod')->name('cod.')->group(function () {
    Route::get('/login','Auth\LoginController@showLoginForm')->name('login');
    Route::post('/login','Auth\LoginController@login')->name('login.submit');
    Route::get('/register','Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('/register','Auth\RegisterController@register')->name('register.submit');
    Route::get('/new/address','Auth\RegisterController@addressView')->name('new.address');
    Route::get('/dashboard', 'Shippers\ShipperDashboardController@index')->name('dashboard');
    Route::get('/order/management', 'Shippers\ShipperDashboardController@orderList');
    Route::get('/order/pending', 'Shippers\ShipperDashboardController@orderPending');
    Route::get('/shipment/book', 'Shippers\ShipperDashboardController@shipmentBookView');
    Route::get('/shipment/book/order_id', 'Shippers\ShipperDashboardController@shipmentBookOrderID');
    Route::post('/shipment/book', 'Shippers\ShipperDashboardController@shipmentBookStore');
    Route::post('/shipment/print_air_waybill', 'Shippers\ShipperDashboardController@printAirWaybill');

    Route::prefix('shipment')->name('shipment.')->group(function () {
        Route::prefix('receiving_sheet')->name('receiving_sheet.')->group(function () {
            Route::get('list', 'Shippers\ShipperReceivingSheetController@list')->name('list');
            Route::get('all', 'Shippers\ShipperReceivingSheetController@all')->name('all');
            Route::put('add', 'Shippers\ShipperReceivingSheetController@add')->name('add');
            Route::put('void', 'Shippers\ShipperReceivingSheetController@void')->name('void');
            Route::post('print', 'Shippers\ShipperReceivingSheetController@print')->name('print');
        });

        Route::resource('receiving_sheet', 'Shippers\ShipperReceivingSheetController');
    });

    Route::get('/logout','Auth\LoginController@logout')->name('logout');
    Route::get('/register/success','Auth\RegisterController@register_success');
    Route::post('/logout','Auth\LoginController@logout')->name('logout');
    Route::get('/name/match/{name}','Auth\RegisterController@checkCompanyName');
});

Route::prefix('admin')->group(function () {
    Route::get('/login','Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login','Auth\AdminLoginController@login')->name('admin.login.submit');
    Route::get('/dashboard', 'Admins\AdminDashboardController@index')->name('admin.dashboard');
    Route::get('/order/management', 'Admins\AdminDashboardController@orderList');
    Route::get('/order/pending', 'Admins\AdminDashboardController@orderPending');
    Route::get('/accounts/pending', 'Admins\AdminDashboardController@pendingAccountsList')->name('admin.accounts.pending');
   
   //Datatables data using ajax calls
    Route::get('/accounts/active/ajax', 'Admins\AdminDashboardController@activeAccountListAjax')->name('admin.accounts.active.ajax');
    Route::get('/accounts/pending/ajax', 'Admins\AdminDashboardController@pendingAccountListAjax')->name('admin.accounts.pending.ajax');
    Route::get('/accounts/block/ajax', 'Admins\AdminDashboardController@blockAccountListAjax')->name('admin.accounts.block.ajax');

    Route::get('/accounts/active', 'Admins\AdminDashboardController@activeAccountsList')->name('admin.accounts.active');
    Route::get('/accounts/block', 'Admins\AdminDashboardController@blockAccountsList');
    //add rates view
    Route::get('/accounts/{id}/add/rates','Admins\AdminDashboardController@addRatesView')->name('admin.add.rates');
    Route::post('/accounts/{id}/add/rates','Admins\AdminDashboardController@addRates')->name('admin.add.rates.submit');
    //edit rates
    Route::get('/accounts/{id}/edit/rates','Admins\AdminDashboardController@editRatesView')->name('admin.edit.rates');
    Route::post('/accounts/{id}/edit/rates','Admins\AdminDashboardController@editRates')->name('admin.edit.rates.submit');

    //ajax request
    Route::post('/account/status', 'Admins\AdminDashboardController@UserStatus')->name('admin.account.status');
    //new address


    Route::get('/logout','Auth\AdminLoginController@logout')->name('admin.logout');
    Route::post('/logout','Auth\AdminLoginController@logout')->name('admin.logout');
    Route::get('/accounts/pending/{id}/bank' ,'Admins\AdminDashboardController@viewBankInfo');
    Route::get('/accounts/pending/{id}/shipping' ,'Admins\AdminDashboardController@viewShippingInfo');
    Route::get('/accounts/pending/{id}/rates' ,'Admins\AdminDashboardController@viewShipperRates');
    Route::get('/pickup', 'Admins\AdminDashboardController@pickup');
    //Reset Password
    Route::post('password/email','Auth\AdminForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
    Route::get('password/reset','Auth\AdminForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
    Route::post('password/reset','Auth\AdminResetPasswordController@reset');
    Route::get('password/reset/{token}','Auth\AdminResetPasswordController@showResetForm')->name('admin.password.reset');
});
