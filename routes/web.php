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

        Route::prefix('receiving_sheet_history')->name('receiving_sheet_history.')->group(function () {
            Route::get('list', 'Shippers\ShipperReceivingSheetHistoryController@list')->name('list');
            Route::post('booked_shipments', 'Shippers\ShipperReceivingSheetHistoryController@booked_shipments')->name('booked_shipments');
            Route::post('received_shipments', 'Shippers\ShipperReceivingSheetHistoryController@received_shipments')->name('received_shipments');
            Route::post('short_received_shipments', 'Shippers\ShipperReceivingSheetHistoryController@short_received_shipments')->name('short_received_shipments');
            Route::put('void', 'Shippers\ShipperReceivingSheetHistoryController@void')->name('void');
            Route::post('create', 'Shippers\ShipperReceivingSheetHistoryController@create')->name('create');
        });

        Route::resource('receiving_sheet_history', 'Shippers\ShipperReceivingSheetHistoryController');
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
    Route::put('/accounts/{id}/edit/rates','Admins\AdminDashboardController@editRates')->name('edit.rates.submit');

    //ajax request
    Route::put('/account/status', 'Admins\AdminDashboardController@UserStatus')->name('account.status');
    //new address
    Route::prefix('management')->name('management.')->group(function () {

        Route::get('/city', 'Admins\AdminDashboardController@cityView')->name('city.index');
        Route::get('/city/ajax', 'Admins\AdminDashboardController@cityListAjax')->name('city.ajax');
        Route::get('/city/form', 'Admins\AdminDashboardController@getCityForm');
        Route::get('/city/{id}/edit/form', 'Admins\AdminDashboardController@getEditCityForm')->name('city.edit');
        Route::put('/city/{id}/edit/form', 'Admins\AdminDashboardController@updateCity')->name('city.edit');
        Route::post('/city', 'Admins\AdminDashboardController@addCityHub')->name('city');
        Route::put('/city/status', 'Admins\AdminDashboardController@CityStatus')->name('city.status');
        Route::get('/city/{id}/status/ajax', 'Admins\AdminDashboardController@CityStatusCheck')->name('city.status.ajax');

        //Route
        Route::prefix('route')->name('route.')->group(function () {
        Route::get('/','Admins\AdminDashboardController@routeView')->name('index');
        Route::get('ajax', 'Admins\AdminDashboardController@routeListAjax')->name('ajax');
        Route::get('/add', 'Admins\AdminDashboardController@addRouteView')->name('add');
        Route::post('/add', 'Admins\AdminDashboardController@addRouteDetails')->name('add');
        Route::get('{id}/edit', 'Admins\AdminDashboardController@editRouteView')->name('edit');
        Route::put('{id}/edit', 'Admins\AdminDashboardController@editRouteDetails')->name('edit');
        Route::put('/status', 'Admins\AdminDashboardController@routeStatus')->name('status');
        });
        Route::prefix('rider')->name('rider.')->group(function (){
            Route::get('','Admins\AdminDashboardController@riderView')->name('index');
            Route::get('ajax', 'Admins\AdminDashboardController@riderListAjax')->name('ajax');
            Route::get('/add', 'Admins\AdminDashboardController@addRiderView')->name('add');
            Route::get('categoryAjax', 'Admins\AdminDashboardController@categoryListAjax')->name('category.ajax');
            Route::post('/add', 'Admins\AdminDashboardController@addRiderDetails')->name('add');
            Route::get('{id}/edit', 'Admins\AdminDashboardController@editRiderView')->name('edit');
            Route::put('{id}/edit', 'Admins\AdminDashboardController@editRiderDetails')->name('edit');
            Route::put('/status', 'Admins\AdminDashboardController@riderStatus')->name('status');
        });
    });
	Route::prefix('pickups')->name('pickups.')->group(function () {
        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('', 'Admins\AdminPickupsController@pending_index')->name('index');
            Route::get('/list', 'Admins\AdminPickupsController@pending_list')->name('list');
            Route::put('assign', 'Admins\AdminPickupsController@pending_assign')->name('assign');
            Route::put('multiple_cancel', 'Admins\AdminPickupsController@pending_multiple_cancel')->name('multiple_cancel');
            Route::put('cancel', 'Admins\AdminPickupsController@pending_cancel')->name('cancel');
        });

        Route::prefix('assigned')->name('assigned.')->group(function () {
            Route::get('', 'Admins\AdminPickupsController@assigned_index')->name('index');
            Route::get('list', 'Admins\AdminPickupsController@assigned_list')->name('list');
            Route::put('cancel', 'Admins\AdminPickupsController@assigned_cancel')->name('cancel');
            Route::post('view_details', 'Admins\AdminPickupsController@assigned_view_details')->name('view_details');
            Route::put('generate_pickup_note', 'Admins\AdminPickupsController@assigned_generate_pickup_note')->name('generate_pickup_note');
            Route::post('print', 'Admins\AdminPickupsController@assigned_print')->name('print');
        });

        Route::prefix('receive')->name('receive.')->group(function () {
            Route::get('', 'Admins\AdminPickupsController@receive_index')->name('index');
            Route::get('list', 'Admins\AdminPickupsController@receive_list')->name('list');
            Route::post('pickup_note', 'Admins\AdminPickupsController@receive_pickup_note')->name('pickup_note');
            Route::post('shipment_details', 'Admins\AdminPickupsController@receive_shipment_details')->name('shipment_details');

            Route::prefix('arrival_of_shipments')->name('arrival_of_shipments.')->group(function () {
                Route::get('', 'Admins\AdminPickupsController@receive_arrival_of_shipments_index')->name('index');
                Route::post('', 'Admins\AdminPickupsController@receive_arrival_of_shipments_store')->name('store');
            });

            Route::prefix('summary')->name('summary.')->group(function () {
                Route::get('', 'Admins\AdminPickupsController@receive_summary_index')->name('index');
                Route::get('list', 'Admins\AdminPickupsController@receive_summary_list')->name('list');

                Route::prefix('request')->name('request.')->group(function () {
                    Route::post('short_received', 'Admins\AdminPickupsController@receive_summary_request_short_received')->name('short_received');
                    Route::put('done', 'Admins\AdminPickupsController@receive_summary_request_done')->name('done');
                    Route::put('not_done', 'Admins\AdminPickupsController@receive_summary_request_not_done')->name('not_done');
                });
            });
        });
    });
    Route::prefix('delivery')->name('delivery.')->group(function(){
        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('','Admins\DeliveryController@pending_delivery_index')->name('index');
            Route::get('list','Admins\DeliveryController@pending_list')->name('list');
        });
        Route::prefix('note')->name('note.')->group(function () {
            Route::get('','Admins\DeliveryController@delivery_note_index')->name('index');
            Route::get('shipment/info','Admins\DeliveryController@get_shipment_details')->name('shipment.info');
            Route::post('create','Admins\DeliveryController@create_delivery_note')->name('create');

        });
        Route::prefix('receive')->name('receive.')->group(function (){
            Route::get('','Admins\DeliveryController@delivery_note_receive_index')->name('index');
            Route::get('list','Admins\DeliveryController@receive_deliveries_list')->name('list');
            Route::get('tracking/search','Admins\DeliveryController@receive_delivery_search')->name('tracking.search');
            Route::get('{id}/update','Admins\DeliveryController@receive_delivery_update')->name('update');
            Route::get('{id}/update/list','Admins\DeliveryController@receive_delivery_notes_list')->name('update.list');
            Route::get('update/remove','Admins\DeliveryController@receive_delivery_remove')->name('update.remove');
            Route::post('print','Admins\DeliveryController@received_print')->name('print');
            Route::get('{id}/status','Admins\DeliveryController@receive_delivery_status_view')->name('status');
            Route::post('add/status','Admins\DeliveryController@receive_delivery_status_submit')->name('add.status');
            Route::post('dn/verify','Admins\DeliveryController@receive_delivery_note_verify')->name('dn.verify');

            Route::get('{id}/add/list','Admins\DeliveryController@receive_delivery_status_list')->name('add.list');
            Route::post('reason','Admins\DeliveryController@receive_delivery_reason')->name('reason');
            Route::post('delivered','Admins\DeliveryController@receive_delivery_status_delivered')->name('delivered');
            Route::post('shipmentstatuscheck','Admins\DeliveryController@receive_delivery_status_check')->name('shipmentstatuscheck');
            Route::post('replacements','Admins\DeliveryController@receive_delivery_get_replacements')->name('replacements');
            Route::put('replacements.submit','Admins\DeliveryController@receive_delivery_replacements_submit')->name('replacements.submit');
            Route::post('trybuys','Admins\DeliveryController@receive_delivery_get_trybuys')->name('trybuys');
            Route::put('trybuys.submit','Admins\DeliveryController@receive_delivery_trybuys_submit')->name('trybuys.submit');
            Route::get('{id}/status/verify','Admins\DeliveryController@receive_delivery_note_verify_view')->name('status.verify');
            Route::get('{id}/verify/status/list','Admins\DeliveryController@receive_delivery_verify_status_list')->name('verify.status.list');
            Route::put('verify/status/submit','Admins\DeliveryController@receive_delivery_verify_status_submit')->name('verify.status.submit');
            Route::post('dncc/print','Admins\DeliveryController@dncc_print')->name('dncc.print');

        });
        Route::prefix('completed')->name('completed.')->group(function(){
            Route::get('','Admins\DeliveryController@completed_deliveries_index')->name('index');
            Route::get('list','Admins\DeliveryController@completed_receive_deliveries_list')->name('list');

        });
    });

    Route::prefix('cargo')->name('cargo.')->group(function () {
        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@pending_index')->name('index');
            Route::get('/list', 'Admins\AdminCargoController@pending_list')->name('list');
        });
    });

    Route::get('/logout','Auth\AdminLoginController@logout')->name('logout');
    Route::post('/logout','Auth\AdminLoginController@logout')->name('logout');
    Route::get('/accounts/pending/{id}/bank' ,'Admins\AdminDashboardController@viewBankInfo');
    Route::get('/accounts/pending/{id}/shipping' ,'Admins\AdminDashboardController@viewShippingInfo');
    Route::get('/accounts/pending/{id}/rates' ,'Admins\AdminDashboardController@viewShipperRates');
    //Reset Password
    Route::post('password/email','Auth\AdminForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset','Auth\AdminForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/reset','Auth\AdminResetPasswordController@reset');
    Route::get('password/reset/{token}','Auth\AdminResetPasswordController@showResetForm')->name('password.reset');

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::prefix('pickup')->name('pickup.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@pickup_index')->name('index');
            Route::post('weight/add', 'Admins\GlobalSettingsController@add_pickup_weight')->name('weight.add');
            Route::put('weight/add', 'Admins\GlobalSettingsController@add_pickup_weight')->name('weight.add');
        });
    });
});
