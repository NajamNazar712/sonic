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
    return redirect()->route('cod.login');
});


Auth::routes();

Route::prefix('tracking')->name('tracking.')->group(function () {
    Route::get('{tracking_number?}', 'TrackingController@index')->name('index');
    Route::post('track', 'TrackingController@track')->name('track');
});

Route::prefix('cod')->name('cod.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('cod.login');
    });

    Route::get('/login','Auth\LoginController@showLoginForm')->name('login');
    Route::post('/login','Auth\LoginController@login')->name('login.submit');
    Route::get('/register','Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('/register','Auth\RegisterController@register')->name('register.submit');
    Route::get('/new/address','Auth\RegisterController@addressView')->name('new.address');

    Route::get('access_denied', 'Shippers\ShipperDashboardController@access_denied')->name('access_denied');

    Route::get('/dashboard', 'Shippers\ShipperDashboardController@orders_index')->name('dashboard');
    Route::get('/order/pending', 'Shippers\ShipperDashboardController@orderPending');

    Route::prefix('orders')->name('orders.')->group(function(){
        Route::get('','Shippers\ShipperDashboardController@orders_index')->name('index');
        Route::get('list','Shippers\ShipperDashboardController@orders_list')->name('list');
        Route::post('search','Shippers\ShipperDashboardController@statistics_search')->name('search');
        Route::post('cancel','Shippers\ShipperDashboardController@order_cancel')->name('cancel');
        Route::post('cancel_all', 'Shippers\ShipperDashboardController@order_cancel_all')->name('cancel_all');
        Route::post('shipment_charges','Shippers\ShipperDashboardController@get_shipment_charges')->name('charges');
    });
    Route::prefix('shipment')->name('shipment.')->group(function () {
        Route::prefix('book')->name('book.')->group(function () {
            Route::get('index', 'Shippers\ShipperShipmentBookController@corporate_index')->name('corporate.index');
            Route::post('corporate_store', 'Shippers\ShipperShipmentBookController@corporate_store')->name('corporate.store');
//            Route::get('order_id', 'Shippers\ShipperShipmentBookController@order_id')->name('order_id');
            Route::post('shipping_modes', 'Shippers\ShipperShipmentBookController@shipping_modes')->name('shipping_modes');
            Route::post('corporate_shipping_modes', 'Shippers\ShipperShipmentBookController@corporate_shipping_modes')->name('corporate_shipping_modes');
            Route::post('corporate_min_chargeable_weight', 'Shippers\ShipperShipmentBookController@corporate_min_chargeable_weight')->name('corporate_min_chargeable_weight');
            Route::post('print_air_waybill', 'Shippers\ShipperShipmentBookController@print_air_waybill')->name('print_air_waybill');
            Route::post('corporate_invoice', 'Shippers\ShipperShipmentBookController@corporate_invoice')->name('corporate_invoice');
            Route::post('check', 'Shippers\ShipperShipmentBookController@check')->name('check');
			Route::post('shipment_check', 'Shippers\ShipperShipmentBookController@shipment_check')->name('shipment_check');
            Route::get('get_consignee_infos', 'Shippers\ShipperShipmentBookController@get_consignee_infos')->name('get_consignee_infos');
            Route::post('get_consignee_info', 'Shippers\ShipperShipmentBookController@get_consignee_info')->name('get_consignee_info');

            Route::post('check_cod_cap_zone_classes', 'Shippers\ShipperShipmentBookController@check_cod_cap_zone_classes')->name('check_cod_cap_zone_classes');

            Route::prefix('excel')->name('excel_')->group(function () {
                Route::get('', 'Shippers\ShipperShipmentBookController@excel_index')->name('index');
                Route::post('', 'Shippers\ShipperShipmentBookController@excel_store')->name('store');
            });
            Route::prefix('corporate_excel')->name('corporate_excel_')->group(function () {
                Route::get('', 'Shippers\ShipperShipmentBookController@corporate_excel_index')->name('index');
                Route::post('', 'Shippers\ShipperShipmentBookController@corporate_excel_store')->name('store');
            });
        });

        Route::resource('book', 'Shippers\ShipperShipmentBookController');

        Route::prefix('receiving_sheet')->name('receiving_sheet.')->group(function () {
            Route::get('list', 'Shippers\ShipperReceivingSheetController@list')->name('list');
            Route::get('receiving_sheet_list', 'Shippers\ShipperReceivingSheetController@receiving_sheet_list')->name('receiving_sheet_list');
            Route::get('all', 'Shippers\ShipperReceivingSheetController@all')->name('all');
            Route::put('add', 'Shippers\ShipperReceivingSheetController@add')->name('add');
            Route::put('void', 'Shippers\ShipperReceivingSheetController@void')->name('void');
            Route::post('print', 'Shippers\ShipperReceivingSheetController@print')->name('print');
            Route::get('new','Shippers\ShipperReceivingSheetController@create_view')->name('new');
            Route::post('info','Shippers\ShipperReceivingSheetController@get_shipment_details')->name('info');
        });

        Route::resource('receiving_sheet', 'Shippers\ShipperReceivingSheetController');

        Route::prefix('receiving_sheet_history')->name('receiving_sheet_history.')->group(function () {
            Route::get('short_received_list', 'Shippers\ShipperReceivingSheetHistoryController@short_received_list')->name('short_received_list');
            Route::get('receiving_sheet_list', 'Shippers\ShipperReceivingSheetHistoryController@receiving_sheet_list')->name('receiving_sheet_list');
            Route::post('booked_shipments', 'Shippers\ShipperReceivingSheetHistoryController@booked_shipments')->name('booked_shipments');
            Route::post('received_shipments', 'Shippers\ShipperReceivingSheetHistoryController@received_shipments')->name('received_shipments');
            Route::post('short_received_shipments', 'Shippers\ShipperReceivingSheetHistoryController@short_received_shipments')->name('short_received_shipments');
            Route::put('void', 'Shippers\ShipperReceivingSheetHistoryController@void')->name('void');
            Route::post('create', 'Shippers\ShipperReceivingSheetHistoryController@create')->name('create');
        });

        Route::resource('receiving_sheet_history', 'Shippers\ShipperReceivingSheetHistoryController');
    });

    Route::prefix('dispute')->name('dispute.')->group(function (){
        Route::get('','Shippers\ShipperDisputeController@dispute_index')->name('index');
        Route::get('list','Shippers\ShipperDisputeController@dispute_list')->name('list');
        Route::post('create','Shippers\ShipperDisputeController@dispute_create')->name('create');
        Route::post('get/shipments','Shippers\ShipperDisputeController@get_shipments')->name('get.shipments');
        Route::post('get/comments','Shippers\ShipperDisputeController@get_comments')->name('get.comments');
        Route::post('data','Shippers\ShipperDisputeController@get_data')->name('data');
        Route::prefix('rebook')->name('rebook.')->group(function (){
            Route::get('','Shippers\ShipperDisputeController@rebook_index')->name('index');
            Route::get('list','Shippers\ShipperDisputeController@rebook_list')->name('list');
            Route::post('shipment/info','Shippers\ShipperDisputeController@get_shipment_info')->name('shipment.info');
            Route::post('shipment/update','Shippers\ShipperDisputeController@rebook_shipment_update')->name('shipment.update');
        });
    });
    Route::prefix('tracking')->name('tracking.')->group(function () {
        Route::get('{tracking_number?}', 'Shippers\ShipperTrackingController@index')->name('index');
        Route::post('track', 'Shippers\ShipperTrackingController@track')->name('track');
    });
    Route::prefix('packaging')->name('packaging.')->group(function (){
        Route::prefix('requests')->name('requests.')->group(function (){
            Route::get('','Shippers\ShipperPackagingMaterialController@packaging_request')->name('index');
            Route::post('submit','Shippers\ShipperPackagingMaterialController@packaging_request_submit')->name('submit');
        });
    });
    Route::prefix('return')->name('return.')->group(function (){
        Route::prefix('pending')->name('pending.')->group(function (){
            Route::get('','Shippers\ShipperReturnController@confirmation_pending_index')->name('index');
            Route::get('list','Shippers\ShipperReturnController@confirmation_pending_list')->name('list');
            Route::post('marked/status','Shippers\ShipperReturnController@return_marked_status')->name('marked.status');
            Route::post('marked/status/single','Shippers\ShipperReturnController@return_marked_single_status')->name('marked.status.single');
            Route::post('reattempt/status','Shippers\ShipperReturnController@return_reattempt_status')->name('reattempt.status');
            Route::post('reattempt/nsa','Shippers\ShipperReturnController@return_reattempt_nsa')->name('reattempt.nsa');
            Route::post('reattempt/status/single','Shippers\ShipperReturnController@return_reattempt_single_status')->name('reattempt.status.single');
            Route::post('marked/self_collection','Shippers\ShipperReturnController@change_status_to_self_collection')->name('marked.self_collection');
        });
    });

    Route::prefix('substitute_account_management')->name('substitute_account_management.')->group(function() {
        Route::get('', 'Shippers\ShipperSubstituteAccountManagementController@index')->name('index');
        Route::get('list', 'Shippers\ShipperSubstituteAccountManagementController@list')->name('list');
        Route::get('email', 'Shippers\ShipperSubstituteAccountManagementController@email')->name('email');
        Route::post('status', 'Shippers\ShipperSubstituteAccountManagementController@status')->name('status');

        Route::prefix('add')->name('add.')->group(function() {
            Route::get('', 'Shippers\ShipperSubstituteAccountManagementController@add_index')->name('index');
            Route::post('', 'Shippers\ShipperSubstituteAccountManagementController@add_store')->name('store');
        });

        Route::prefix('update/{id}')->name('update.')->group(function() {
            Route::get('', 'Shippers\ShipperSubstituteAccountManagementController@update_index')->name('index');
            Route::post('', 'Shippers\ShipperSubstituteAccountManagementController@update_store')->name('store');
        });
    });

    Route::prefix('finance')->name('finance.')->group(function () {
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('', 'Shippers\ShipperFinanceController@payments_index')->name('index');
            Route::get('list', 'Shippers\ShipperFinanceController@payments_list')->name('list');
            Route::post('delivered_shipments', 'Shippers\ShipperFinanceController@payments_delivered_shipments')->name('delivered_shipments');
            Route::post('returned_shipments', 'Shippers\ShipperFinanceController@payments_returned_shipments')->name('returned_shipments');
            Route::post('adjusted_shipments', 'Shippers\ShipperFinanceController@payments_adjusted_shipments')->name('adjusted_shipments');
            Route::post('details_print', 'Shippers\ShipperFinanceController@payments_details_print')->name('details_print');
            Route::get('export_to_excel', 'Shippers\ShipperFinanceController@payments_export_to_excel')->name('export_to_excel');
        });
    });

    Route::prefix('reports')->name('reports.')->group(function (){
        Route::prefix('qsr')->name('qsr.')->group(function (){
            Route::get('','Shippers\ShipperReportsController@qsr_index')->name('index');
            Route::get('list','Shippers\ShipperReportsController@qsr_list')->name('list');
        });
        Route::prefix('sales')->name('sales.')->group(function (){
            Route::get('','Shippers\ShipperReportsController@sales_index')->name('index');
            Route::get('list','Shippers\ShipperReportsController@sales_list')->name('list');
        });
        Route::prefix('summary')->name('summary.')->group(function (){
            Route::get('','Shippers\ShipperReportsController@summary_index')->name('index');
            Route::get('list','Shippers\ShipperReportsController@summary_list')->name('list');
            Route::post('data','Shippers\ShipperReportsController@summary_data')->name('data');
        });
    });

    Route::get('/logout','Auth\LoginController@logout')->name('logout');
    Route::get('/register/success','Auth\RegisterController@register_success');
    Route::post('/logout','Auth\LoginController@logout')->name('logout');
    Route::get('/name/match/{name}','Auth\RegisterController@checkCompanyName');
    Route::get('/email/match/{email}/{id}','Auth\RegisterController@checkCompanyEmail')->name('check.email');
    Route::get('/name/match/{name}/{id}','Auth\RegisterController@checkCompanyNameProfile')->name('check.name');


    //user profile
    Route::get('/profile','Shippers\ShipperDashboardController@userProfile')->name('edit.profile');
    Route::post('updateprofile','Shippers\ShipperDashboardController@updateProfile')->name('update.profile');
    Route::get('getpickups','Shippers\ShipperDashboardController@getPickups')->name('get.pickups');
    Route::post('changepickupstatus','Shippers\ShipperDashboardController@pickupStatusChange')->name('change.pickup.status');
    Route::post('addpickup','Shippers\ShipperDashboardController@addPickup')->name('add.pickup');
    Route::post('updateprofile','Shippers\ShipperDashboardController@updateProfile')->name('update.profile');
    Route::post('edit/emails','Shippers\ShipperDashboardController@edit_notification_emails')->name('edit.emails');
    Route::post('add/emails','Shippers\ShipperDashboardController@add_notification_emails')->name('add.emails');

	Route::prefix('resources')->name('resources.')->group(function (){
	        Route::get('','Shippers\ShipperResourcesController@index')->name('index');
	        Route::get('city_list','Shippers\ShipperResourcesController@get_network_list')->name('city_list');
	    });
	
	Route::prefix('crm')->name('crm.')->group(function () {
	        Route::prefix('request')->name('request.')->group(function(){
	            Route::get('', 'Shippers\ShipperCRMController@index')->name('index');
	            Route::get('list', 'Shippers\ShipperCRMController@requests_list')->name('list');
	            Route::get('{id}/details', 'Shippers\ShipperCRMController@request_details')->name('details');
	            Route::post('add', 'Shippers\ShipperCRMController@add_request')->name('add');
	        });
	        Route::prefix('feedback')->name('feedback.')->group(function(){
	            Route::post('add', 'Shippers\ShipperCRMController@add_feedback')->name('add');
	        });
	        Route::prefix('comment')->name('comment.')->group(function(){
	            Route::post('add', 'Shippers\ShipperCRMController@add_comment')->name('add');
	            Route::post('get', 'Shippers\ShipperCRMController@get_latest_comment')->name('get');
	        });
	    });

    Route::prefix('cancelled_shipments')->name('cancelled_shipments.')->group(function (){        Route::get('','Shippers\ShipperShipmentCancelController@index')->name('index');
        Route::get('list', 'Shippers\ShipperShipmentCancelController@list')->name('list');
        Route::put('revert', 'Shippers\ShipperShipmentCancelController@revert')->name('revert');
    });
    Route::prefix('intercept')->name('intercept.')->group(function (){
        Route::get('/{row_id}','Shippers\ShipperInterceptReBookController@intercept_re_book_index')->name('index');
        Route::post('update','Shippers\ShipperInterceptReBookController@intercept_re_book_update')->name('update');
    });

});
//Admin Routes Start
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.login');
    });

    Route::get('/login','Auth\AdminLoginController@showLoginForm')->name('login');
    Route::post('/login','Auth\AdminLoginController@login')->name('login.submit');

    Route::get('access_denied', 'Admins\AdminController@access_denied')->name('access_denied');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('', 'Admins\AdminDashboardController@index')->name('index');
        Route::post('search','Admins\AdminDashboardController@statistics_search')->name('search');
    });


    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('', 'Admins\OrderManagementController@index')->name('index');
        Route::get('list', 'Admins\OrderManagementController@orders_list')->name('list');
        Route::post('shipment_charges','Admins\OrderManagementController@get_shipment_charges')->name('charges');
        Route::post('shipper_recall','Admins\OrderManagementController@shipper_recall')->name('shipper_recall');
        Route::get('shipment_print_status', 'Admins\OrderManagementController@shipment_print_status')->name('shipment_print_status');
    });
    Route::get('/order/pending', 'Admins\AdminDashboardController@orderPending');
    Route::prefix('accounts')->name('accounts.')->group(function(){
        Route::get('pending', 'Admins\AdminDashboardController@pendingAccountsList')->name('pending');
        Route::get('pending/ajax', 'Admins\AdminDashboardController@pendingAccountListAjax')->name('pending.ajax');
        Route::get('active', 'Admins\AdminDashboardController@activeAccountsList')->name('active');
        Route::get('active/ajax', 'Admins\AdminDashboardController@activeAccountListAjax')->name('active.ajax');
        Route::get('block', 'Admins\AdminDashboardController@blockAccountsList')->name('block');
        Route::get('block/ajax', 'Admins\AdminDashboardController@blockAccountListAjax')->name('block.ajax');
        Route::post('status/block','Admins\AdminDashboardController@UserStatusBlock')->name('status.block');
        Route::post('status/change','Admins\AdminDashboardController@UserStatusChange')->name('status.change');
        Route::put('status', 'Admins\AdminDashboardController@UserStatus')->name('status');
        Route::post('tag/submit','Admins\AdminDashboardController@tagSubmit')->name('tag.submit');
        Route::post('reject/submit','Admins\AdminDashboardController@rejectReasonSubmit')->name('rejectreason.submit');


        //user profile
        Route::get('/{id}/view','Admins\AdminDashboardController@userProfile')->name('view.profile');
        Route::post('/updateprofile','Admins\AdminDashboardController@updateProfile')->name('update.profile');
        Route::get('getpickups','Admins\AdminDashboardController@getPickups')->name('get.pickups');
        Route::post('/updatebankinfo','Admins\AdminDashboardController@updateBankInfo')->name('update.bank');
        Route::post('edit/emails','Admins\AdminDashboardController@edit_notification_emails')->name('edit.emails');
        Route::post('add/emails','Admins\AdminDashboardController@add_notification_emails')->name('add.emails');
    });

    //Datatables data using ajax calls

    //add rates view
    Route::get('/accounts/{id}/add/rates','Admins\AdminDashboardController@addRatesView')->name('add.rates');
    Route::post('/accounts/{id}/add/rates','Admins\AdminDashboardController@addRates')->name('add.rates.submit');
    //edit rates
    Route::get('/accounts/{id}/edit/rates','Admins\AdminDashboardController@editRatesView')->name('edit.rates');
    Route::put('/accounts/{id}/edit/rates','Admins\AdminDashboardController@editRates')->name('edit.rates.submit');
    //
    Route::get('/accounts/{id}/view/rates','Admins\AdminDashboardController@viewRates')->name('view.rates');
    Route::prefix('corporate')->name('corporate.')->group(function (){
        Route::get('{id}/add/rates','Admins\AdminCorporateAccountsController@add_rates_index')->name('add.rates');
        Route::post('{id}/add/rates','Admins\AdminCorporateAccountsController@add_rates_submit')->name('add.rates');
        Route::get('{id}/edit/rates','Admins\AdminCorporateAccountsController@edit_rates_index')->name('edit.rates');
        Route::put('{id}/edit/rates','Admins\AdminCorporateAccountsController@edit_rates_submit')->name('edit.rates');
        Route::get('{id}/view/rates','Admins\AdminCorporateAccountsController@view_rates_index')->name('view.rates');

    });
    //ajax request


    //new address
    Route::prefix('management')->name('management.')->group(function () {

        Route::get('/city', 'Admins\AdminDashboardController@cityView')->name('city.index');
        Route::get('/city/ajax', 'Admins\AdminDashboardController@cityListAjax')->name('city.ajax');
        Route::get('/city/form', 'Admins\AdminDashboardController@getCityForm')->name('city.form');
        Route::get('/city/{id}/edit/form', 'Admins\AdminDashboardController@getEditCityForm')->name('city.edit');
        Route::put('/city/{id}/edit/form', 'Admins\AdminDashboardController@updateCity')->name('city.edit');
        Route::post('/city', 'Admins\AdminDashboardController@addCityHub')->name('city');
        Route::put('/city/status', 'Admins\AdminDashboardController@CityStatus')->name('city.status');
        Route::get('/city/{id}/status/ajax', 'Admins\AdminDashboardController@CityStatusCheck')->name('city.status.ajax');
        Route::get('', 'Admins\AdminDashboardController@walk_in_city_list')->name('city_list');
        Route::post('', 'Admins\AdminDashboardController@check_min_charges')->name('min_charges');

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

        Route::prefix('zonal')->name('zonal.')->group(function () {
            Route::get('', 'Admins\AdminZonalManagementController@index')->name('index');
            Route::get('/list', 'Admins\AdminZonalManagementController@list')->name('list');

            Route::prefix('add')->name('add.')->group(function () {
                Route::get('', 'Admins\AdminZonalManagementController@add_index')->name('index');
                Route::post('', 'Admins\AdminZonalManagementController@add_store')->name('store');
            });

            Route::prefix('update/{id}')->name('update.')->group(function () {
                Route::get('', 'Admins\AdminZonalManagementController@update_index')->name('index');
                Route::post('', 'Admins\AdminZonalManagementController@update_store')->name('store');
            });

            Route::post('view_cities', 'Admins\AdminZonalManagementController@view_cities')->name('view_cities');
        });
    });
    Route::prefix('pickups')->name('pickups.')->group(function () {
        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('', 'Admins\AdminPickupsController@pending_index')->name('index');
            Route::get('/list', 'Admins\AdminPickupsController@pending_list')->name('list');
            Route::put('assign', 'Admins\AdminPickupsController@pending_assign')->name('assign');
            Route::put('multiple_cancel', 'Admins\AdminPickupsController@pending_multiple_cancel')->name('multiple_cancel');
            Route::put('cancel', 'Admins\AdminPickupsController@pending_cancel')->name('cancel');
            Route::post('bookings/all','Admins\AdminPickupsController@pending_all_bookings')->name('bookings.all');
            Route::post('bookings','Admins\AdminPickupsController@pending_bookings')->name('bookings');
        });

        Route::prefix('assigned')->name('assigned.')->group(function () {
            Route::get('', 'Admins\AdminPickupsController@assigned_index')->name('index');
            Route::get('list', 'Admins\AdminPickupsController@assigned_list')->name('list');
            Route::put('cancel', 'Admins\AdminPickupsController@assigned_cancel')->name('cancel');
            Route::post('view_details', 'Admins\AdminPickupsController@assigned_view_details')->name('view_details');
            Route::post('print', 'Admins\AdminPickupsController@assigned_print')->name('print');
            Route::post('sms', 'Admins\AdminPickupsController@assigned_sms')->name('sms');
            Route::post('bookings/all','Admins\AdminPickupsController@assigned_all_bookings')->name('bookings.all');
            Route::post('pickups','Admins\AdminPickupsController@assigned_pickups')->name('pickups');
        });

        Route::prefix('receive')->name('receive.')->group(function () {
            Route::get('', 'Admins\AdminPickupsController@receive_index')->name('index');
            Route::get('list', 'Admins\AdminPickupsController@receive_list')->name('list');
            Route::post('pickup_note', 'Admins\AdminPickupsController@receive_pickup_note')->name('pickup_note');
            Route::post('shipment_details', 'Admins\AdminPickupsController@receive_shipment_details')->name('shipment_details');
            Route::post('shipment_remove', 'Admins\AdminPickupsController@receive_shipment_remove')->name('shipment_remove');
            Route::post('bookings/all','Admins\AdminPickupsController@receive_all_bookings')->name('bookings.all');

            Route::prefix('arrival_of_shipments')->name('arrival_of_shipments.')->group(function () {
                Route::get('', 'Admins\AdminPickupsController@receive_arrival_of_shipments_index')->name('index');
                Route::post('', 'Admins\AdminPickupsController@receive_arrival_of_shipments_store')->name('store');
            });

            Route::prefix('summary')->name('summary.')->group(function () {
                Route::get('', 'Admins\AdminPickupsController@receive_summary_index')->name('index');
                Route::get('list', 'Admins\AdminPickupsController@receive_summary_list')->name('list');
                Route::post('bookings/all','Admins\AdminPickupsController@summary_receive_all_bookings')->name('bookings.all');
                Route::post('received','Admins\AdminPickupsController@summary_receive_shipments')->name('received');

                Route::prefix('request')->name('request.')->group(function () {
                    Route::post('short_received', 'Admins\AdminPickupsController@receive_summary_request_short_received')->name('short_received');
                    Route::post('over_received', 'Admins\AdminPickupsController@receive_summary_request_over_received')->name('over_received');
                    Route::post('over_short_received', 'Admins\AdminPickupsController@receive_summary_request_over_short_received')->name('over_short_received');
                    Route::put('done', 'Admins\AdminPickupsController@receive_summary_request_done')->name('done');
                    Route::put('not_done', 'Admins\AdminPickupsController@receive_summary_request_not_done')->name('not_done');
                });
            });
        });
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('', 'Admins\AdminPickupsController@history_index')->name('index');
            Route::get('list', 'Admins\AdminPickupsController@history_list')->name('list');
            Route::post('bookings/all','Admins\AdminPickupsController@history_bookings')->name('bookings.all');
        });
        Route::prefix('bookedvsreceived')->name('bookedvsreceived.')->group(function (){
            Route::get('', 'Admins\AdminPickupsController@bookedvsreceived_index')->name('index');
            Route::post('list', 'Admins\AdminPickupsController@bookedvsreceived_list')->name('list');
            Route::post('booked', 'Admins\AdminPickupsController@bookedvsreceived_booked_list')->name('booked');
            Route::post('received', 'Admins\AdminPickupsController@bookedvsreceived_received_list')->name('received');
        });
    });
    Route::prefix('delivery')->name('delivery.')->group(function(){
        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('','Admins\DeliveryController@pending_delivery_index')->name('index');
            Route::get('list','Admins\DeliveryController@pending_list')->name('list');
        });
        Route::prefix('note')->name('note.')->group(function () {
            Route::get('','Admins\DeliveryController@delivery_note_index')->name('index');
            Route::post('shipment/info','Admins\DeliveryController@get_shipment_details')->name('shipment.info');
            Route::post('create','Admins\DeliveryController@create_delivery_note')->name('create');

        });
        Route::prefix('cash_collection')->name('cash_collection.')->group(function (){
            Route::prefix('pending')->name('pending.')->group(function () {
                Route::get('', 'Admins\DeliveryController@pending_cash_collection_index')->name('index');
                Route::get('list', 'Admins\DeliveryController@pending_cash_collection_list')->name('list');
                Route::post('collect', 'Admins\DeliveryController@pending_cash_collect')->name('collect');
                Route::post('all','Admins\DeliveryController@pending_cash_collect_all')->name('all');
                Route::post('shipments','Admins\DeliveryController@cash_collection_shipments')->name('shipments');
                Route::post('shipments/delivered','Admins\DeliveryController@cash_collection_shipments_delivered')->name('shipments.delivered');
            });
        });
        Route::prefix('receive')->name('receive.')->group(function (){
            Route::get('','Admins\DeliveryController@delivery_note_receive_index')->name('index');
            Route::get('list','Admins\DeliveryController@receive_deliveries_list')->name('list');
            Route::post('shipments','Admins\DeliveryController@receive_delivery_shipments')->name('shipments');
            Route::get('tracking/search','Admins\DeliveryController@receive_delivery_search')->name('tracking.search');
            Route::get('{id}/update','Admins\DeliveryController@receive_delivery_update')->name('update');
            Route::get('{id}/update/list','Admins\DeliveryController@receive_delivery_notes_list')->name('update.list');
            Route::post('update/remove','Admins\DeliveryController@receive_delivery_remove')->name('update.remove');
            Route::post('print','Admins\DeliveryController@received_print')->name('print');
            Route::get('{id}/status','Admins\DeliveryController@receive_delivery_status_view')->name('status');
            Route::post('add/status','Admins\DeliveryController@receive_delivery_status_submit')->name('add.status');
            Route::post('add/status/all','Admins\DeliveryController@receive_delivery_status_submit_all')->name('add.status.all');
            Route::get('{id}/add/list','Admins\DeliveryController@receive_delivery_status_list')->name('add.list');
            Route::post('reason','Admins\DeliveryController@receive_delivery_reason')->name('reason');
            Route::post('delivered','Admins\DeliveryController@receive_delivery_status_delivered')->name('delivered');
            Route::post('shipmentstatuscheck','Admins\DeliveryController@receive_delivery_status_check')->name('shipmentstatuscheck');
            Route::post('replacements','Admins\DeliveryController@receive_delivery_get_replacements')->name('replacements');
            Route::put('replacements/submit','Admins\DeliveryController@receive_delivery_replacements_submit')->name('replacements.submit');
            Route::post('trybuys','Admins\DeliveryController@receive_delivery_get_trybuys')->name('trybuys');
            Route::put('trybuys.submit','Admins\DeliveryController@receive_delivery_trybuys_submit')->name('trybuys.submit');
            //Non Service Area Routes
            Route::post('nsa_shipments_data','Admins\DeliveryController@nsa_shipments_data')->name('nsa_shipments_data');
            Route::put('nsa_shipments/submit','Admins\DeliveryController@nsa_shipments_submit')->name('nsa_shipments.submit');
            //Non Service Area Routes

            Route::get('{id}/status/verify','Admins\DeliveryController@receive_delivery_note_verify_view')->name('status.verify');
            Route::get('{id}/verify/status/list','Admins\DeliveryController@receive_delivery_verify_status_list')->name('verify.status.list');
            Route::put('verify/status/submit','Admins\DeliveryController@receive_delivery_verify_status_submit')->name('verify.status.submit');
            Route::post('dncc/print','Admins\DeliveryController@dncc_print')->name('dncc.print');
            Route::post('undelivered/print','Admins\DeliveryController@dncc_undelivered_print')->name('undelivered.print');

        });
        Route::prefix('completed')->name('completed.')->group(function(){
            Route::get('','Admins\DeliveryController@completed_deliveries_index')->name('index');
            Route::get('list','Admins\DeliveryController@completed_receive_deliveries_list')->name('list');
            Route::post('deposit/dncc','Admins\DeliveryController@completed_deliveries_selected_dncc')->name('deposit.dncc');
            Route::get('sdn/create','Admins\DeliveryController@create_sdn_view')->name('sdn.create');
            Route::post('sdn/create','Admins\DeliveryController@create_sdn_submit')->name('sdn.create.submit');
            Route::get('dncc/list','Admins\DeliveryController@get_sdn_list')->name('dncc.list');
            Route::post('shipments','Admins\DeliveryController@completed_shipments')->name('shipments');
            Route::post('shipments/delivered','Admins\DeliveryController@completed_shipments_delivered')->name('shipments.delivered');
        });
        Route::prefix('sdn')->name('sdn.')->group(function (){
            Route::get('','Admins\DeliveryController@sdn_view')->name('index');
            Route::get('list','Admins\DeliveryController@sdn_list')->name('list');
            Route::post('dn','Admins\DeliveryController@sdn_dncc_list')->name('dn');
            Route::get('{id}/details','Admins\DeliveryController@sdn_details')->name('details');
            Route::get('{id}/ajax','Admins\DeliveryController@sdn_details_ajax')->name('ajax');
            Route::post('slip','Admins\DeliveryController@sdn_deposit_slip')->name('slip');
            Route::post('print','Admins\DeliveryController@sdn_deposit_slip_print')->name('print');
            Route::post('dncc/print','Admins\DeliveryController@sdn_dncc_print')->name('dncc.print');
            Route::post('shipments','Admins\DeliveryController@sdn_delivered_shipments')->name('shipments');

        });
        Route::prefix('misroute')->name('misroute.')->group(function (){
            Route::get('','Admins\DeliveryController@misroute_index')->name('index');
            Route::get('list','Admins\DeliveryController@misroute_list')->name('list');

            Route::prefix('history')->name('history.')->group(function (){
                Route::get('','Admins\MisroutedHistoryController@misrouted_history_index')->name('index');
                Route::get('list','Admins\MisroutedHistoryController@misrouted_history_list')->name('list');

            });
            Route::prefix('update')->name('update.')->group(function (){
                Route::get('','Admins\DeliveryController@misrouted_update_index')->name('index');
//                Route::get('list','Admins\DeliveryController@misrouted_update_list')->name('list');
                Route::post('shipment/info','Admins\DeliveryController@get_misroute_shipment_info')->name('shipment.info');
                Route::post('store','Admins\DeliveryController@misroute_shipment_update')->name('store');
                Route::get('excel','Admins\DeliveryController@misroute_shipment_excel')->name('excel');
            });

        });
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('', 'Admins\DeliveryController@history_index')->name('index');
            Route::get('list', 'Admins\DeliveryController@history_list')->name('list');
            Route::post('shipments', 'Admins\DeliveryController@history_shipments')->name('shipments');
            Route::post('shipments/delivered', 'Admins\DeliveryController@history_shipments_delivered')->name('shipments.delivered');

        });

        //Lost Module Start
        Route::prefix('lost')->name('lost.')->group(function (){
            Route::get('','Admins\LostShipmentsController@lost_shipments_index')->name('index');
            Route::get('list','Admins\LostShipmentsController@lost_shipments_list')->name('list');
            Route::post('confirm/status','Admins\LostShipmentsController@shipment_confirm_status')->name('confirm.status');
            Route::post('reattempt/status','Admins\LostShipmentsController@shipment_reattempt_status')->name('reattempt.status');
            Route::prefix('add')->name('add.')->group(function(){
                Route::get('index','Admins\LostShipmentsController@lost_add_index')->name('index');
                Route::post('shipment/info','Admins\LostShipmentsController@get_shipment_info')->name('shipment.info');
                Route::post('shipment/store','Admins\LostShipmentsController@add_lost_shipments')->name('shipments.store');
            });
        });
        //Lost Module End

        Route::prefix('intercept')->name('intercept.')->group(function () {
            Route::get('', 'Admins\DeliveryController@intercept_request_index')->name('index');
            Route::get('list', 'Admins\DeliveryController@intercept_request_list')->name('list');
            Route::post('approve', 'Admins\DeliveryController@approve')->name('approve');
            Route::post('reject', 'Admins\DeliveryController@reject')->name('reject');
            Route::prefix('history')->name('history.')->group(function () {
                Route::get('', 'Admins\AdminInterceptRebookRequestHistoryController@intercept_request_history_index')->name('index');
                Route::get('list', 'Admins\AdminInterceptRebookRequestHistoryController@intercept_request_history_list')->name('list');
            });

        });

        Route::prefix('fake_status')->name('fake_status.')->group(function () {
            Route::get('', 'Admins\DeliveryController@fake_status_remove_index')->name('index');
            Route::get('list', 'Admins\DeliveryController@fake_status_remove_list')->name('list');
            Route::post('remove', 'Admins\DeliveryController@fake_status_remove')->name('remove');
        });
        Route::prefix('replacement')->name('replacement.')->group(function () {
            Route::prefix('not_collected')->name('not_collected.')->group(function () {
                Route::get('', 'Admins\DeliveryController@replacement_not_collected_index')->name('index');
                Route::get('list', 'Admins\DeliveryController@replacement_not_collected_list')->name('list');
                Route::post('re_attempt', 'Admins\DeliveryController@replacement_not_collected_re_attempt')->name('re_attempt');
                Route::post('regular_re_attempt', 'Admins\DeliveryController@replacement_not_collected_regular_re_attempt')->name('regular_re_attempt');
            });
            Route::prefix('collected')->name('collected.')->group(function () {
                Route::get('', 'Admins\DeliveryController@replacement_collected_index')->name('index');
                Route::post('list', 'Admins\DeliveryController@change_shipment_booking_type_shipment_details')->name('shipment_details');
                Route::post('change_booking_type', 'Admins\DeliveryController@change_shipment_booking_type')->name('change_booking_type');
            });
            Route::prefix('logs')->name('logs.')->group(function () {
                Route::get('', 'Admins\DeliveryController@replacement_to_regular_logs_index')->name('index');
                Route::get('list', 'Admins\DeliveryController@replacement_to_regular_logs_list')->name('list');
            });
        });
    });
    Route::prefix('return')->name('return.')->group(function (){
        Route::get('','Admins\ReturnController@return_view')->name('index');
        Route::get('list','Admins\ReturnController@return_marked_list')->name('list');
        Route::post('confirm/status','Admins\ReturnController@return_confirm_status')->name('confirm.status');
        Route::post('reattempt/status','Admins\ReturnController@return_reattempt_status')->name('reattempt.status');
        Route::post('marked/status/single','Admins\ReturnController@return_marked_single_status')->name('marked.status.single');
        Route::get('confirmed','Admins\ReturnController@return_confirmed_view')->name('confirmed');
        Route::get('confirmed/list','Admins\ReturnController@return_confirmed_list')->name('confirmed.list');
        Route::post('confirmed/search','Admins\ReturnController@return_confirmed_search')->name('confirmed.search');
        Route::post('excel/store','Admins\ReturnController@excel_store')->name('excel.store');

        Route::post('marked/self_collection','Admins\ReturnController@change_status_to_self_collection')->name('marked.self_collection');
        Route::post('edit/estimated_charges','Admins\ReturnController@update_estimated_charges')->name('edit.estimated_charges');

        Route::prefix('confirmed')->name('confirmed.')->group(function (){
            Route::post('revert','Admins\ReturnController@return_confirmed_revert')->name('revert');
        });

        Route::prefix('create')->name('create.')->group(function(){
            Route::get('','Admins\ReturnController@return_create_index')->name('index');
            Route::post('shipment_details','Admins\ReturnController@get_shipment_details')->name('shipment_details');
            Route::post('note/submit','Admins\ReturnController@return_create_note')->name('note.submit');
        });
        Route::prefix('receive')->name('receive.')->group(function (){
            Route::get('','Admins\ReturnController@return_receive_deliveries_view')->name('index');
            Route::get('list','Admins\ReturnController@return_receive_deliveries_list')->name('list');
            Route::get('{id}/update','Admins\ReturnController@return_receive_update')->name('update');
            Route::get('{id}/update/list','Admins\ReturnController@return_receive_update_list')->name('update.list');
            Route::get('update/remove','Admins\ReturnController@return_receive_update_remove')->name('update.remove');
            Route::get('{id}/status','Admins\ReturnController@return_receive_status')->name('status');
            Route::post('status/submit','Admins\ReturnController@receive_return_status_submit')->name('status.submit');
            Route::post('status/delivered','Admins\ReturnController@return_status_delivered')->name('status.delivered');
            Route::get('status/list','Admins\ReturnController@return_receive_status_list')->name('status.list');
            Route::post('reason','Admins\ReturnController@receive_return_reason')->name('reason');
            Route::post('rn.print','Admins\ReturnController@rrd_print')->name('rn.print');
            Route::post('shipments','Admins\ReturnController@receive_return_shipments')->name('shipments');

        });
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('', 'Admins\ReturnController@history_index')->name('index');
            Route::get('list', 'Admins\ReturnController@history_list')->name('list');
            Route::post('shipments', 'Admins\ReturnController@history_shipments')->name('shipments');

        });
    });
    Route::prefix('cargo')->name('cargo.')->group(function () {
        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@pending_index')->name('index');
            Route::get('list', 'Admins\AdminCargoController@pending_list')->name('list');
        });

        Route::prefix('create')->name('create.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@create_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminCargoController@create_shipment_details')->name('shipment_details');
            Route::post('consignment_details', 'Admins\AdminCargoController@create_consignment_details')->name('consignment_details');
            Route::get('seal_number', 'Admins\AdminCargoController@create_consignment_seal_number')->name('seal_number');
            Route::post('', 'Admins\AdminCargoController@create_store')->name('store');
        });

        Route::prefix('in_transit')->name('in_transit.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@in_transit_index')->name('index');
            Route::get('list', 'Admins\AdminCargoController@in_transit_list')->name('list');
            Route::post('print', 'Admins\AdminCargoController@in_transit_print')->name('print');
            Route::post('junctions', 'Admins\AdminCargoController@in_transit_junctions')->name('junctions');
            Route::post('details', 'Admins\AdminCargoController@in_transit_details')->name('details');
            Route::post('receive_at_link', 'Admins\AdminCargoController@in_transit_receive_at_link')->name('receive_at_link');
            Route::post('forwarding_details', 'Admins\AdminCargoController@in_transit_forwarding_details')->name('forwarding_details');
            Route::post('update', 'Admins\AdminCargoController@in_transit_update')->name('update');
            Route::post('receive', 'Admins\AdminCargoController@in_transit_receive')->name('receive');
            Route::post('shipments', 'Admins\AdminCargoController@in_transit_shipments')->name('shipments');
        });

        Route::prefix('receive')->name('receive.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@receive_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminCargoController@receive_shipment_details')->name('shipment_details');
            Route::post('short_received', 'Admins\AdminCargoController@receive_short_received')->name('short_received');
            Route::post('', 'Admins\AdminCargoController@receive_store')->name('store');
        });
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@history_index')->name('index');
            Route::get('list', 'Admins\AdminCargoController@history_list')->name('list');
            Route::post('shipments', 'Admins\AdminCargoController@history_shipments')->name('shipments');
            Route::post('print', 'Admins\AdminCargoController@history_cargo_print')->name('print');
        });

        Route::prefix('draft')->name('draft.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@draft_index')->name('index');
            Route::get('list', 'Admins\AdminCargoController@draft_list')->name('list');
            Route::post('add', 'Admins\AdminCargoController@draft_add')->name('add');
            Route::post('shipments', 'Admins\AdminCargoController@draft_shipments')->name('shipments');
            Route::prefix('edit')->name('edit.')->group(function (){
                Route::get('{draft}', 'Admins\AdminCargoController@edit_draft_index')->name('index');
                Route::get('{draft}/list', 'Admins\AdminCargoController@edit_draft_list')->name('list');
                Route::post('update', 'Admins\AdminCargoController@draft_update')->name('update');
            });

        });

        Route::prefix('mapping')->name('mapping.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@mapping_index')->name('index');
            Route::get('list', 'Admins\AdminCargoController@mapping_list')->name('list');
            Route::post('store', 'Admins\AdminCargoController@mapping_store')->name('store');
            Route::post('edit', 'Admins\AdminCargoController@mapping_edit')->name('edit');
            Route::post('update', 'Admins\AdminCargoController@mapping_edit_update')->name('update');
//            Route::post('print', 'Admins\AdminCargoController@history_cargo_print')->name('print');
        });

    });
    Route::prefix('dispute')->name('dispute.')->group(function (){
        Route::get('','Admins\DisputeController@dispute_index')->name('index');
        Route::get('list','Admins\DisputeController@dispute_list')->name('list');
        Route::post('create','Admins\DisputeController@dispute_create')->name('create');
        Route::post('get/shipments','Admins\DisputeController@get_shipments')->name('get.shipments');
        Route::post('resolve','Admins\DisputeController@resolve_dispute')->name('resolve');
        Route::post('update','Admins\DisputeController@update_dispute_view')->name('update');
        Route::put('update/submit','Admins\DisputeController@update_dispute')->name('update.submit');
        Route::post('data','Admins\DisputeController@get_data')->name('data');
        Route::post('create/universal','Admins\DisputeController@dispute_create_universal')->name('create.universal');

    });

    Route::prefix('tracking')->name('tracking.')->group(function() {
        Route::get('{tracking_number?}', 'Admins\AdminTrackingController@index')->name('index');
        Route::post('track', 'Admins\AdminTrackingController@track')->name('track');
        Route::post('rider_information', 'Admins\AdminTrackingController@rider_information')->name('rider_information');
        Route::post('cargo_consignment_details', 'Admins\AdminTrackingController@cargo_consignment_details')->name('cargo_consignment_details');
    });

    Route::prefix('quick_tracking')->name('quick_tracking.')->group(function() {
        Route::get('', 'Admins\AdminTrackingController@quick_tracking_index')->name('index');
        Route::post('info', 'Admins\AdminTrackingController@quick_tracking_shipment_info')->name('info');
    });
    Route::prefix('cx_quick_tracking')->name('cx_quick_tracking.')->group(function() {
        Route::get('', 'Admins\AdminTrackingController@cx_quick_tracking_index')->name('cx_index');
        Route::get('list', 'Admins\AdminTrackingController@cx_quick_tracking_list')->name('cx_list');
    });
        Route::prefix('user_management')->name('user_management.')->group(function() {
        Route::prefix('users')->name('users.')->group(function() {
            Route::get('', 'Admins\UserManagementController@user_index')->name('index');
            Route::get('list', 'Admins\UserManagementController@user_list')->name('list');
            Route::get('email', 'Admins\UserManagementController@user_email')->name('email');
            Route::post('status', 'Admins\UserManagementController@user_status')->name('status');

            Route::prefix('add')->name('add.')->group(function() {
                Route::get('', 'Admins\UserManagementController@user_add_index')->name('index');
                Route::post('', 'Admins\UserManagementController@user_add_store')->name('store');
            });

            Route::prefix('update/{id}')->name('update.')->group(function() {
                Route::get('', 'Admins\UserManagementController@user_update_index')->name('index');
                Route::post('', 'Admins\UserManagementController@user_update_store')->name('store');
            });
        });

        Route::prefix('roles')->name('roles.')->group(function() {
            Route::get('', 'Admins\UserManagementController@role_index')->name('index');
            Route::get('list', 'Admins\UserManagementController@role_list')->name('list');

            Route::prefix('add')->name('add.')->group(function() {
                Route::get('', 'Admins\UserManagementController@role_add_index')->name('index');
                Route::post('', 'Admins\UserManagementController@role_add_store')->name('store');
            });

            Route::prefix('update/{id}')->name('update.')->group(function() {
                Route::get('', 'Admins\UserManagementController@role_update_index')->name('index');
                Route::post('', 'Admins\UserManagementController@role_update_store')->name('store');
            });
        });
    });

    Route::prefix('finance')->name('finance.')->group(function () {
        Route::prefix('outstanding_sdn')->name('outstanding_sdn.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@outstanding_sdn_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@outstanding_sdn_list')->name('list');
            Route::post('dncc', 'Admins\AdminFinanceController@outstanding_sdn_dncc')->name('dncc');
            Route::post('dncc/print', 'Admins\AdminFinanceController@outstanding_sdn_dncc_print')->name('dncc.print');
            Route::post('shipments/delivered', 'Admins\AdminFinanceController@outstanding_sdn_shipments_delivered')->name('shipments.delivered');
            Route::get('delivery_notes_list', 'Admins\AdminFinanceController@outstanding_sdn_delivery_notes_list')->name('delivery_notes_list');
            Route::post('reconcile_delivery_notes', 'Admins\AdminFinanceController@outstanding_sdn_reconcile_delivery_notes')->name('reconcile_delivery_notes');
            Route::get('export_to_excel', 'Admins\AdminFinanceController@outstanding_sdn_export_to_excel')->name('export_to_excel');
        });

        Route::prefix('outstanding_shipments')->name('outstanding_shipments.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@outstanding_shipments_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@outstanding_shipments_list')->name('list');
            Route::put('resolved', 'Admins\AdminFinanceController@outstanding_shipments_resolved')->name('resolved');
            Route::put('adjust_in_payment', 'Admins\AdminFinanceController@outstanding_shipments_adjust_in_payment')->name('adjust_in_payment');
            Route::post('dncc/print', 'Admins\AdminFinanceController@outstanding_shipments_dncc_print')->name('dncc.print');
            Route::post('sdn/print', 'Admins\AdminFinanceController@outstanding_shipments_sdn_print')->name('sdn.print');
            Route::get('walk_in_index', 'Admins\AdminFinanceController@outstanding_walk_in_shipments_index')->name('walk_in_index');
            Route::get('walk_in_list', 'Admins\AdminFinanceController@outstanding_walk_in_shipments_list')->name('walk_in_list');
            Route::put('walk_in_resolved', 'Admins\AdminFinanceController@outstanding_walk_in_shipments_resolved')->name('walk_in_resolved');
        });

        Route::prefix('change_shipment_amount')->name('change_shipment_amount.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@change_shipment_amount_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminFinanceController@change_shipment_amount_shipment_details')->name('shipment_details');
            Route::post('', 'Admins\AdminFinanceController@change_shipment_amount_store')->name('store');
        });

        Route::prefix('change_shipment_weight')->name('change_shipment_weight.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@change_shipment_weight_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminFinanceController@change_shipment_weight_shipment_details')->name('shipment_details');
            Route::post('', 'Admins\AdminFinanceController@change_shipment_weight_store')->name('store');
        });

        Route::prefix('add_shipment_adjustment')->name('add_shipment_adjustment.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@add_shipment_adjustment_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminFinanceController@add_shipment_adjustment_shipment_details')->name('shipment_details');
            Route::post('', 'Admins\AdminFinanceController@add_shipment_adjustment_store')->name('store');
        });

        Route::prefix('make_payments')->name('make_payments.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@make_payments_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@make_payments_list')->name('list');
            Route::post('delivered_shipments', 'Admins\AdminFinanceController@make_payments_delivered_shipments')->name('delivered_shipments');
            Route::post('returned_shipments', 'Admins\AdminFinanceController@make_payments_returned_shipments')->name('returned_shipments');
            Route::post('adjusted_shipments', 'Admins\AdminFinanceController@make_payments_adjusted_shipments')->name('adjusted_shipments');
            Route::post('shipment_details', 'Admins\AdminFinanceController@make_payments_shipment_details')->name('shipment_details');
            Route::get('shipment_list', 'Admins\AdminFinanceController@make_payments_shipment_list')->name('shipment_list');
            Route::get('shipment_export_selected', 'Admins\AdminFinanceController@make_payments_shipment_export_selected')->name('shipment_export_selected');
            Route::post('verify', 'Admins\AdminFinanceController@make_payments_verify')->name('verify');
            Route::get('export_bank_order', 'Admins\AdminFinanceController@make_payments_export_bank_order')->name('export_bank_order');
            Route::post('store', 'Admins\AdminFinanceController@make_payments_store')->name('store');
        });

        Route::prefix('done_payments')->name('done_payments.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@done_payments_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@done_payments_list')->name('list');
            Route::put('paid', 'Admins\AdminFinanceController@done_payments_paid')->name('paid');
            Route::put('reverted', 'Admins\AdminFinanceController@done_payments_reverted')->name('reverted');
            Route::post('delivered_shipments', 'Admins\AdminFinanceController@done_payments_delivered_shipments')->name('delivered_shipments');
            Route::post('returned_shipments', 'Admins\AdminFinanceController@done_payments_returned_shipments')->name('returned_shipments');
            Route::post('adjusted_shipments', 'Admins\AdminFinanceController@done_payments_adjusted_shipments')->name('adjusted_shipments');
            Route::post('details_print', 'Admins\AdminFinanceController@done_payments_details_print')->name('details_print');
            Route::post('details', 'Admins\AdminFinanceController@done_payments_details')->name('details');
            Route::put('update_details', 'Admins\AdminFinanceController@done_payments_update_details')->name('update_details');
            Route::get('export_to_excel', 'Admins\AdminFinanceController@done_payments_export_to_excel')->name('export_to_excel');
        });

        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@invoices_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@invoices_list')->name('list');
            Route::post('print', 'Admins\AdminFinanceController@invoices_print')->name('print');
            Route::get('export_to_excel', 'Admins\AdminFinanceController@invoices_export_to_excel')->name('export_to_excel');
            Route::put('email_reminder', 'Admins\AdminFinanceController@invoices_email_reminder')->name('email_reminder');
            Route::post('mark_as_received', 'Admins\AdminFinanceController@invoices_mark_as_received')->name('mark_as_received');
        });
    });

    Route::prefix('petty_cash')->name('petty_cash.')->group(function() {
        Route::prefix('make')->name('make.')->group(function (){
           Route::get('', 'Admins\AdminPettyCashController@make_petty_cash_statement_index')->name('index');
            Route::post('reference', 'Admins\AdminPettyCashController@make_petty_cash_statement_check_reference')->name('reference');
            Route::post('titles', 'Admins\AdminPettyCashController@make_petty_cash_statement_titles')->name('titles');
            Route::post('submit', 'Admins\AdminPettyCashController@make_petty_cash_statement_submit')->name('submit');
        });
        Route::prefix('statements')->name('statements.')->group(function (){
           Route::get('', 'Admins\AdminPettyCashController@petty_cash_statements_index')->name('index');
           Route::get('list', 'Admins\AdminPettyCashController@petty_cash_statements_list')->name('list');
            Route::post('print', 'Admins\AdminPettyCashController@statement_print')->name('print');
            Route::post('approve', 'Admins\AdminPettyCashController@petty_cash_statements_approve')->name('approve');
            Route::get('{id}/edit', 'Admins\AdminPettyCashController@edit_petty_cash_statement_index')->name('edit');
            Route::get('{id}/edit/list', 'Admins\AdminPettyCashController@edit_petty_cash_statement_list')->name('edit.list');
            Route::post('edit/approve', 'Admins\AdminPettyCashController@edit_petty_cash_statements_approve')->name('edit.approve');
            Route::post('edit/reject', 'Admins\AdminPettyCashController@edit_petty_cash_statements_reject')->name('edit.reject');
            Route::put('edit/submit', 'Admins\AdminPettyCashController@edit_petty_cash_statements_submit')->name('edit.submit');
            Route::post('view/amount', 'Admins\AdminPettyCashController@edit_petty_cash_statements_amount')->name('view.amount');


        });
        Route::prefix('approved')->name('approved.')->group(function (){
            Route::get('', 'Admins\AdminPettyCashController@approved_petty_cash_statements_index')->name('index');
            Route::get('list', 'Admins\AdminPettyCashController@approved_petty_cash_statements_list')->name('list');
            Route::post('paid', 'Admins\AdminPettyCashController@approved_petty_cash_statements_paid')->name('paid');
            Route::post('adjusted', 'Admins\AdminPettyCashController@approved_petty_cash_statements_adjusted')->name('adjusted');
        });
    });

    Route::prefix('month_closing')->name('month_closing.')->group(function (){
        Route::get('','Admins\AdminMonthClosingController@month_closing_index')->name('index');
        Route::get('list','Admins\AdminMonthClosingController@month_closing_list')->name('list');
        Route::post('add','Admins\AdminMonthClosingController@add_shipment')->name('add');
        Route::post('confirm','Admins\AdminMonthClosingController@return_confirm_shipment')->name('confirm');
        Route::post('reattempt','Admins\AdminMonthClosingController@return_reattempt_shipment')->name('reattempt');
    });


    Route::prefix('sameday')->name('sameday.')->group(function (){
        Route::get('','Admins\SamedayController@sameday_index')->name('index');
        Route::get('list','Admins\SamedayController@sameday_list')->name('list');
    });
    Route::prefix('packaging')->name('packaging.')->group(function (){
        Route::get('','Admins\AdminPackagingMaterialController@packaging_index')->name('index');
        Route::get('list','Admins\AdminPackagingMaterialController@packaging_list')->name('list');
        Route::post('add/submit','Admins\AdminPackagingMaterialController@add_stock')->name('add.submit');
        Route::get('fetch/cities','Admins\AdminPackagingMaterialController@fetch_cities')->name('fetch.cities');
        Route::post('send/submit','Admins\AdminPackagingMaterialController@send_stock')->name('send.submit');
        Route::prefix('requests')->name('requests.')->group(function (){
            Route::get('','Admins\AdminPackagingMaterialController@request_index')->name('index');
            Route::get('list','Admins\AdminPackagingMaterialController@request_list')->name('list');
            Route::post('dispatch','Admins\AdminPackagingMaterialController@request_dispatch_submit')->name('dispatch');
        });
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('', 'Admins\AdminNotificationsController@index')->name('index');
        Route::get('list', 'Admins\AdminNotificationsController@list')->name('list');
        Route::post('send_custom_email', 'Admins\AdminNotificationsController@send_custom_email')->name('send_custom_email');
        Route::post('details', 'Admins\AdminNotificationsController@details')->name('details');
        Route::post('status', 'Admins\AdminNotificationsController@status')->name('status');
        Route::post('edit', 'Admins\AdminNotificationsController@edit')->name('edit');
    });

    //Reports start
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::prefix('qsr')->name('qsr.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@qsr_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@qsr_list')->name('list');
        });
        Route::prefix('return_note')->name('return_note.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@return_note_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@return_note_list')->name('list');
            Route::post('shipments', 'Admins\AdminReportsController@return_note_shipments')->name('shipments');
        });
        Route::prefix('pickup_note')->name('pickup_note.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@pickup_note_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@pickup_note_list')->name('list');
            Route::post('bookings','Admins\AdminReportsController@pickup_note_bookings')->name('bookings');
        });
        Route::prefix('cargo_received')->name('cargo_received.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@cargo_received_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@cargo_received_list')->name('list');
            Route::post('shipments','Admins\AdminReportsController@cargo_shipments')->name('shipments');
        });
        Route::prefix('lead_time')->name('lead_time.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@lead_time_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@lead_time_list')->name('list');
        });
        Route::prefix('qa')->name('qa.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@qa_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@qa_list')->name('list');
        });
        Route::prefix('outstanding_shipments')->name('outstanding_shipments.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@outstanding_shipments_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@outstanding_shipments_list')->name('list');
        });
        Route::prefix('daily_pickup_sales')->name('daily_pickup_sales.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@daily_pickup_sales_index')->name('index');
            Route::post('export_to_excel', 'Admins\AdminReportsController@daily_pickup_sales_export_to_excel')->name('export_to_excel');
            Route::post('download', 'Admins\AdminReportsController@daily_pickup_sales_download')->name('download');
        });
        Route::prefix('customer_sales')->name('customer_sales.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@customer_sales_index')->name('index');
            Route::post('export_to_excel', 'Admins\AdminReportsController@customer_sales_export_to_excel')->name('export_to_excel');
            Route::get('download', 'Admins\AdminReportsController@customer_sales_download')->name('download');
        });
        Route::prefix('completed_delivery_notes')->name('completed_delivery_notes.')->group(function(){
            Route::get('','Admins\AdminReportsController@completed_delivery_notes_index')->name('index');
            Route::get('list','Admins\AdminReportsController@completed_delivery_notes_list')->name('list');
            Route::post('shipments','Admins\AdminReportsController@completed_shipments')->name('shipments');
            Route::post('shipments/delivered','Admins\AdminReportsController@completed_shipments_delivered')->name('shipments.delivered');
        });
        Route::prefix('customer_retention')->name('customer_retention.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@customer_retention_index')->name('index');
            Route::post('export_to_excel', 'Admins\AdminReportsController@customer_retention_export_to_excel')->name('export_to_excel');
            Route::get('download', 'Admins\AdminReportsController@customer_retention_download')->name('download');
        });
        Route::prefix('overall_sales')->name('overall_sales.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@overall_sales_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@overall_sales_list')->name('list');

        });
        Route::prefix('petty_cash')->name('petty_cash.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@petty_cash_statements_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@petty_cash_statements_list')->name('list');
        });
        Route::prefix('negative_balance_customers')->name('negative_balance_customers.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@negative_balance_customers_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@negative_balance_customers_list')->name('list');

        });
        Route::prefix('call_verification')->name('call_verification.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@call_verification_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@call_verification_list')->name('list');

        });
        Route::prefix('sales_person_performance')->name('sales_person_performance.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@sales_person_performance_index')->name('index');
            Route::post('export_to_excel', 'Admins\AdminReportsController@sales_person_performance_export_to_excel')->name('export_to_excel');
            Route::get('download', 'Admins\AdminReportsController@sales_person_performance_download')->name('download');
        });

		Route::prefix('fake_status')->name('fake_status.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@fake_status_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@fake_status_list')->name('list');
            Route::post('shipments/total','Admins\AdminReportsController@fake_status_shipments_total')->name('shipments.total');
            Route::post('shipments/undelivered','Admins\AdminReportsController@fake_status_shipments_undelivered')->name('shipments.undelivered');
            Route::post('fake_status_shipment','Admins\AdminReportsController@fake_status_shipments')->name('fake_status_shipment');
        });

		Route::prefix('debriefing')->name('debriefing.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@debriefing_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@debriefing_list')->name('list');
            Route::get('export', 'Admins\AdminReportsController@debriefing_export')->name('export');
        });
        Route::prefix('cargo_returns_shipment')->name('cargo_returns_shipment.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@cargo_returns_shipment_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@cargo_returns_shipment_list')->name('list');

        });
        Route::prefix('return_reattempt_ratio')->name('return_reattempt_ratio.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@return_reattempt_ratio_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@return_reattempt_ratio_list')->name('list');

        });
        Route::prefix('multiple_payment_report')->name('multiple_payment_report.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@multiple_payment_report_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@multiple_payment_report_list')->name('list');

        });
		Route::prefix('revenue')->name('revenue.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@revenue_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@revenue_list')->name('list');
        });
		Route::prefix('crm')->name('crm.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@crm_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@crm_list')->name('list');

        });
		Route::prefix('gst')->name('gst.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@gst_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@gst_list')->name('list');
        });

		Route::prefix('summary')->name('summary.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@summary_index')->name('index');
            Route::post('data', 'Admins\AdminReportsController@summary_data')->name('data');
            Route::get('list', 'Admins\AdminReportsController@summary_list')->name('list');
        });
    });

    //Reports end

    Route::prefix('cancelled_shipments')->name('cancelled_shipments.')->group(function (){
        Route::get('','Admins\AdminShipmentCancelController@index')->name('index');
        Route::get('list', 'Admins\AdminShipmentCancelController@list')->name('list');
        Route::put('revert', 'Admins\AdminShipmentCancelController@revert')->name('revert');
    });

    Route::get('/logout','Auth\AdminLoginController@logout')->name('logout');
    Route::post('/logout','Auth\AdminLoginController@logout')->name('logout');
    Route::get('/accounts/pending/{id}/bank' ,'Admins\AdminDashboardController@viewBankInfo');
    Route::get('/accounts/pending/{id}/shipping' ,'Admins\AdminDashboardController@viewShippingInfo');
    Route::get('/accounts/pending/{id}/rates' ,'Admins\AdminDashboardController@viewShipperRates');
    //Reset Password
    Route::post('password/email','Auth\AdminForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset','Auth\AdminForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/reset','Auth\AdminResetPasswordController@reset')->name('password.reset');
    Route::get('password/reset/{token}','Auth\AdminResetPasswordController@showResetForm')->name('password.reset');

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::prefix('pickup')->name('pickup.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@pickup_index')->name('index');
            Route::post('weight/add', 'Admins\GlobalSettingsController@add_pickup_weight')->name('weight.add');
            Route::put('weight/add', 'Admins\GlobalSettingsController@add_pickup_weight')->name('weight.add');
        });

        Route::prefix('shipment_cancellation_cut_off_days')->name('shipment_cancellation_cut_off_days.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@shipment_cancellation_cut_off_days_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@shipment_cancellation_cut_off_days_store')->name('store');
        });
        Route::prefix('auto_account_disabled_days')->name('auto_account_disabled_days.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@auto_account_disabled_days_index')->name('auto_index');
            Route::post('', 'Admins\GlobalSettingsController@auto_account_disabled_days_store')->name('auto_store');
        });
        Route::prefix('daily_pickup_sales_cron')->name('daily_pickup_sales_cron.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@daily_pickup_sales_cron_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@daily_pickup_sales_cron_store')->name('store');
        });
        Route::prefix('non_service_area')->name('non_service_area.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@non_service_area_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@non_service_area_store')->name('store');
        });

        Route::prefix('ticker')->name('ticker.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@ticker_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@ticker_store')->name('store');
        });
        Route::prefix('walk_in')->name('walk_in.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@walk_in_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@walk_in_store')->name('store');
        });
        Route::prefix('auto_invoice_generation_and_due_date')->name('auto_invoice_generation_and_due_date.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@auto_invoice_generation_and_due_date_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@auto_invoice_generation_and_due_date_store')->name('store');
        });
        Route::prefix('petty_cash')->name('petty_cash.')->group(function (){
           Route::prefix('heads')->name('heads.')->group(function (){
              Route::get('', 'Admins\GlobalSettingsController@petty_cash_heads_index')->name('index');
              Route::get('list', 'Admins\GlobalSettingsController@petty_cash_heads_list')->name('list');
              Route::post('add', 'Admins\GlobalSettingsController@petty_cash_heads_add')->name('add');
              Route::post('edit', 'Admins\GlobalSettingsController@petty_cash_heads_edit')->name('edit');
              Route::post('active', 'Admins\GlobalSettingsController@petty_cash_heads_active')->name('active');
              Route::post('inactive', 'Admins\GlobalSettingsController@petty_cash_heads_inactive')->name('inactive');
           });
           Route::prefix('titles')->name('titles.')->group(function (){
              Route::get('', 'Admins\GlobalSettingsController@petty_cash_titles_index')->name('index');
              Route::get('list', 'Admins\GlobalSettingsController@petty_cash_titles_list')->name('list');
              Route::post('add', 'Admins\GlobalSettingsController@petty_cash_titles_add')->name('add');
              Route::post('info', 'Admins\GlobalSettingsController@petty_cash_titles_info')->name('info');
              Route::post('edit', 'Admins\GlobalSettingsController@petty_cash_titles_edit')->name('edit');
              Route::post('active', 'Admins\GlobalSettingsController@petty_cash_titles_active')->name('active');
              Route::post('inactive', 'Admins\GlobalSettingsController@petty_cash_titles_inactive')->name('inactive');
           });
        });

        Route::prefix('debriefing_report_cut_off_time')->name('debriefing_report_cut_off_time.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@debriefing_report_cut_off_time_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@debriefing_report_cut_off_time_store')->name('store');
        });

		 Route::prefix('fuel_factor')->name('fuel_factor.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@fuel_factor_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@fuel_factor_store')->name('store');
        });


        Route::prefix('return_note_restriction_bypass')->name('return_note_restriction_bypass.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@return_note_restriction_bypass_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@return_note_restriction_bypass_store')->name('store');
        });

        Route::prefix('cod_cap_zones')->name('cod_cap_zones.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@cod_cap_zones_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@cod_cap_zones_update')->name('update');
        });
    });
    Route::prefix('shipment')->name('shipment.')->group(function () {
        Route::prefix('book')->name('book.')->group(function () {
            Route::get('', 'Admins\AdminWalkInBookShipmentController@index')->name('walk_in');
//            Route::get('order_id', 'Admins\AdminWalkInBookShipmentController@order_id')->name('order_id');
            Route::post('store', 'Admins\AdminWalkInBookShipmentController@walk_in_store')->name('store');
            Route::post('add_fuel_surcharge_gst_total', 'Admins\AdminWalkInBookShipmentController@add_fuel_surcharge_gst_total')->name('add_fuel_surcharge_gst_total');
            Route::post('print_air_waybill', 'Admins\AdminWalkInBookShipmentController@print_air_waybill')->name('print_air_waybill');
            Route::post('check_standard_weight', 'Admins\AdminWalkInBookShipmentController@check_standard_weight')->name('check_standard_weight');
            Route::post('check_min_charges', 'Admins\AdminWalkInBookShipmentController@check_min_charges')->name('check_min_charges');
        });
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('', 'Admins\AdminWalkInBookShipmentController@history_index')->name('walk_in_history');
            Route::get('list', 'Admins\AdminWalkInBookShipmentController@history_list')->name('walk_in_history_list');
        });
    });

    //CMC Routes
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::prefix('request')->name('request.')->group(function(){
            Route::post('add', 'Admins\AdminCRMController@add_request')->name('add');
            Route::post('get_request', 'Admins\AdminCRMController@get_request_info')->name('get_request');
            Route::post('update', 'Admins\AdminCRMController@update_request')->name('update');
            Route::get('', 'Admins\AdminCRMController@launched')->name('launched');
            Route::get('{id}', 'Admins\AdminCRMController@request_details')->name('details');
            Route::post('edit', 'Admins\AdminCRMController@edit_request')->name('edit');
        });
        Route::prefix('feedback')->name('feedback.')->group(function(){
            Route::post('add', 'Admins\AdminCRMController@add_feedback')->name('add');
        });
        Route::prefix('launched_re_open')->name('launched_re_open.')->group(function(){
            Route::get('', 'Admins\AdminCRMController@launched_re_open_index')->name('index');
            Route::get('list', 'Admins\AdminCRMController@launched_re_open_list')->name('list');
        });
        Route::prefix('in_process')->name('in_process.')->group(function(){
            Route::get('', 'Admins\AdminCRMController@in_process_index')->name('index');
            Route::get('list', 'Admins\AdminCRMController@in_process_list')->name('list');
        });
        Route::prefix('resolved')->name('resolved.')->group(function(){
            Route::get('', 'Admins\AdminCRMController@resolved_index')->name('index');
            Route::get('list', 'Admins\AdminCRMController@resolved_list')->name('list');
        });
        Route::prefix('closed')->name('closed.')->group(function(){
            Route::get('', 'Admins\AdminCRMController@closed_index')->name('index');
            Route::get('list', 'Admins\AdminCRMController@closed_list')->name('list');
        });
        Route::post('assign', 'Admins\AdminCRMController@assign')->name('assign');
        Route::post('close', 'Admins\AdminCRMController@close')->name('close');
        Route::post('valid', 'Admins\AdminCRMController@valid')->name('valid');
        Route::post('invalid', 'Admins\AdminCRMController@invalid')->name('invalid');
        Route::post('tag', 'Admins\AdminCRMController@admin_tag')->name('tag');
        Route::prefix('comment')->name('comment.')->group(function(){
            Route::post('add', 'Admins\AdminCRMController@add_comment')->name('add');
            Route::post('get', 'Admins\AdminCRMController@get_latest_comment')->name('get');
        });

        Route::get('permissions', 'Admins\AdminCRMController@crm_index')->name('permissions');
        Route::get('list', 'Admins\AdminCRMController@crm_list')->name('list');

        Route::prefix('update/{id}')->name('update.')->group(function() {
            Route::get('', 'Admins\AdminCRMController@crm_update_index')->name('index');
            Route::post('', 'Admins\AdminCRMController@crm_update_store')->name('store');
        });
    });
});

