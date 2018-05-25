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

    Route::prefix('shipment')->name('shipment.')->group(function () {
        Route::prefix('book')->name('book.')->group(function () {
            Route::get('order_id', 'Shippers\ShipperShipmentBookController@order_id')->name('order_id');
            Route::post('print_air_waybill', 'Shippers\ShipperShipmentBookController@print_air_waybill')->name('print_air_waybill');

            Route::prefix('excel')->name('excel_')->group(function () {
                Route::get('', 'Shippers\ShipperShipmentBookController@excel_index')->name('index');
                Route::post('', 'Shippers\ShipperShipmentBookController@excel_store')->name('store');
            });
        });

        Route::resource('book', 'Shippers\ShipperShipmentBookController');

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

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login','Auth\AdminLoginController@showLoginForm')->name('login');
    Route::post('/login','Auth\AdminLoginController@login')->name('login.submit');
    Route::get('/dashboard', 'Admins\AdminDashboardController@index')->name('dashboard');
    Route::get('/order/management', 'Admins\AdminDashboardController@orderList');
    Route::get('/order/pending', 'Admins\AdminDashboardController@orderPending');
    Route::get('/accounts/pending', 'Admins\AdminDashboardController@pendingAccountsList')->name('accounts.pending');
   
   //Datatables data using ajax calls
    Route::get('/accounts/active/ajax', 'Admins\AdminDashboardController@activeAccountListAjax')->name('accounts.active.ajax');
    Route::get('/accounts/pending/ajax', 'Admins\AdminDashboardController@pendingAccountListAjax')->name('accounts.pending.ajax');
    Route::get('/accounts/block/ajax', 'Admins\AdminDashboardController@blockAccountListAjax')->name('accounts.block.ajax');

    Route::get('/accounts/active', 'Admins\AdminDashboardController@activeAccountsList')->name('accounts.active');
    Route::get('/accounts/block', 'Admins\AdminDashboardController@blockAccountsList');
    //add rates view
    Route::get('/accounts/{id}/add/rates','Admins\AdminDashboardController@addRatesView')->name('add.rates');
    Route::post('/accounts/{id}/add/rates','Admins\AdminDashboardController@addRates')->name('add.rates.submit');
    //edit rates
    Route::get('/accounts/{id}/edit/rates','Admins\AdminDashboardController@editRatesView')->name('edit.rates');
    Route::post('/accounts/{id}/edit/rates','Admins\AdminDashboardController@editRates')->name('edit.rates.submit');

    //ajax request
    Route::post('/account/status', 'Admins\AdminDashboardController@UserStatus')->name('account.status');
    //new address

    Route::prefix('pickups')->name('pickups.')->group(function () {
        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('', 'Admins\AdminPickupsController@pending_index')->name('index');
            Route::get('/list', 'Admins\AdminPickupsController@pending_list')->name('list');
            Route::put('assign', 'Admins\AdminPickupsController@pending_assign')->name('assign');
            Route::put('multiple_cancel', 'Admins\AdminPickupsController@pending_multiple_cancel')->name('multiple_cancel');
            Route::put('cancel', 'Admins\AdminPickupsController@pending_cancel')->name('cancel');
        });
    });

    Route::get('/logout','Auth\AdminLoginController@logout')->name('logout');
    Route::post('/logout','Auth\AdminLoginController@logout')->name('logout');
    Route::get('/accounts/pending/{id}/bank' ,'Admins\AdminDashboardController@viewBankInfo');
    Route::get('/accounts/pending/{id}/shipping' ,'Admins\AdminDashboardController@viewShippingInfo');
    Route::get('/accounts/pending/{id}/rates' ,'Admins\AdminDashboardController@viewShipperRates');
    Route::get('/pickup', 'Admins\AdminDashboardController@pickup');
    //Reset Password
    Route::post('password/email','Auth\AdminForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset','Auth\AdminForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/reset','Auth\AdminResetPasswordController@reset');
    Route::get('password/reset/{token}','Auth\AdminResetPasswordController@showResetForm')->name('password.reset');
});
