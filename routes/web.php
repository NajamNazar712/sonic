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

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/ecommerce', 'Shippers\ShipperDashboardController@ecommerce');

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
       Route::post('shipment_charges','Shippers\ShipperDashboardController@get_shipment_charges')->name('charges');
    });
    Route::prefix('shipment')->name('shipment.')->group(function () {
        Route::prefix('book')->name('book.')->group(function () {
            Route::get('order_id', 'Shippers\ShipperShipmentBookController@order_id')->name('order_id');
            Route::post('shipping_modes', 'Shippers\ShipperShipmentBookController@shipping_modes')->name('shipping_modes');
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
    });

    Route::get('/logout','Auth\LoginController@logout')->name('logout');
    Route::get('/register/success','Auth\RegisterController@register_success');
    Route::post('/logout','Auth\LoginController@logout')->name('logout');
    Route::get('/name/match/{name}','Auth\RegisterController@checkCompanyName');
});
//Admin Routes Start
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.login');
    });

    Route::get('/login','Auth\AdminLoginController@showLoginForm')->name('login');
    Route::post('/login','Auth\AdminLoginController@login')->name('login.submit');

    Route::get('access_denied', 'Admins\AdminController@access_denied')->name('access_denied');

    Route::get('dashboard', 'Admins\AdminDashboardController@index')->name('dashboard');

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('dashboard', 'Admins\AdminDashboardController@index')->name('index');
        Route::get('list', 'Admins\AdminDashboardController@orders_list')->name('list');
        Route::post('search','Admins\AdminDashboardController@statistics_search')->name('search');
        Route::post('shipment_charges','Admins\AdminDashboardController@get_shipment_charges')->name('charges');
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
    });

   //Datatables data using ajax calls

    //add rates view
    Route::get('/accounts/{id}/add/rates','Admins\AdminDashboardController@addRatesView')->name('add.rates');
    Route::post('/accounts/{id}/add/rates','Admins\AdminDashboardController@addRates')->name('add.rates.submit');
    //edit rates
    Route::get('/accounts/{id}/edit/rates','Admins\AdminDashboardController@editRatesView')->name('edit.rates');
    Route::put('/accounts/{id}/edit/rates','Admins\AdminDashboardController@editRates')->name('edit.rates.submit');

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
            Route::post('print', 'Admins\AdminPickupsController@assigned_print')->name('print');
            Route::post('sms', 'Admins\AdminPickupsController@assigned_sms')->name('sms');
        });

        Route::prefix('receive')->name('receive.')->group(function () {
            Route::get('', 'Admins\AdminPickupsController@receive_index')->name('index');
            Route::get('list', 'Admins\AdminPickupsController@receive_list')->name('list');
            Route::post('pickup_note', 'Admins\AdminPickupsController@receive_pickup_note')->name('pickup_note');
            Route::post('shipment_details', 'Admins\AdminPickupsController@receive_shipment_details')->name('shipment_details');
            Route::post('shipment_remove', 'Admins\AdminPickupsController@receive_shipment_remove')->name('shipment_remove');

            Route::prefix('arrival_of_shipments')->name('arrival_of_shipments.')->group(function () {
                Route::get('', 'Admins\AdminPickupsController@receive_arrival_of_shipments_index')->name('index');
                Route::post('', 'Admins\AdminPickupsController@receive_arrival_of_shipments_store')->name('store');
            });

            Route::prefix('summary')->name('summary.')->group(function () {
                Route::get('', 'Admins\AdminPickupsController@receive_summary_index')->name('index');
                Route::get('list', 'Admins\AdminPickupsController@receive_summary_list')->name('list');

                Route::prefix('request')->name('request.')->group(function () {
                    Route::post('short_received', 'Admins\AdminPickupsController@receive_summary_request_short_received')->name('short_received');
                    Route::post('over_received', 'Admins\AdminPickupsController@receive_summary_request_over_received')->name('over_received');
                    Route::post('over_short_received', 'Admins\AdminPickupsController@receive_summary_request_over_short_received')->name('over_short_received');
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
        Route::prefix('cash_collection')->name('cash_collection.')->group(function (){
            Route::prefix('pending')->name('pending.')->group(function () {
                Route::get('', 'Admins\DeliveryController@pending_cash_collection_index')->name('index');
                Route::get('list', 'Admins\DeliveryController@pending_cash_collection_list')->name('list');
                Route::post('collect', 'Admins\DeliveryController@pending_cash_collect')->name('collect');
                Route::post('all','Admins\DeliveryController@pending_cash_collect_all')->name('all');
            });
        });
        Route::prefix('receive')->name('receive.')->group(function (){
            Route::get('','Admins\DeliveryController@delivery_note_receive_index')->name('index');
            Route::get('list','Admins\DeliveryController@receive_deliveries_list')->name('list');
            Route::get('tracking/search','Admins\DeliveryController@receive_delivery_search')->name('tracking.search');
            Route::get('{id}/update','Admins\DeliveryController@receive_delivery_update')->name('update');
            Route::get('{id}/update/list','Admins\DeliveryController@receive_delivery_notes_list')->name('update.list');
            Route::post('update/remove','Admins\DeliveryController@receive_delivery_remove')->name('update.remove');
            Route::post('print','Admins\DeliveryController@received_print')->name('print');
            Route::get('{id}/status','Admins\DeliveryController@receive_delivery_status_view')->name('status');
            Route::post('add/status','Admins\DeliveryController@receive_delivery_status_submit')->name('add.status');
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
            Route::post('undelivered/print','Admins\DeliveryController@dncc_undelivered_print')->name('undelivered.print');

        });
        Route::prefix('completed')->name('completed.')->group(function(){
            Route::get('','Admins\DeliveryController@completed_deliveries_index')->name('index');
            Route::get('list','Admins\DeliveryController@completed_receive_deliveries_list')->name('list');
            Route::post('deposit/dncc','Admins\DeliveryController@completed_deliveries_selected_dncc')->name('deposit.dncc');
            Route::get('sdn/create','Admins\DeliveryController@create_sdn_view')->name('sdn.create');
            Route::post('sdn/create','Admins\DeliveryController@create_sdn_submit')->name('sdn.create.submit');
            Route::get('dncc/list','Admins\DeliveryController@get_sdn_list')->name('dncc.list');
        });
        Route::prefix('sdn')->name('sdn.')->group(function (){
           Route::get('','Admins\DeliveryController@sdn_view')->name('index');
           Route::get('list','Admins\DeliveryController@sdn_list')->name('list');
           Route::get('{id}/details','Admins\DeliveryController@sdn_details')->name('details');
           Route::get('{id}/ajax','Admins\DeliveryController@sdn_details_ajax')->name('ajax');
           Route::post('slip','Admins\DeliveryController@sdn_deposit_slip')->name('slip');
           Route::post('print','Admins\DeliveryController@sdn_deposit_slip_print')->name('print');

        });
        Route::prefix('misroute')->name('misroute.')->group(function (){
           Route::get('','Admins\DeliveryController@misroute_index')->name('index');
           Route::get('list','Admins\DeliveryController@misroute_list')->name('list');
           Route::post('shipment/info','Admins\DeliveryController@get_shipment_info')->name('shipment.info');
           Route::post('shipment/update','Admins\DeliveryController@misroute_shipment_update')->name('shipment.update');

        });
    });
    Route::prefix('return')->name('return.')->group(function (){
        Route::get('','Admins\ReturnController@return_view')->name('index');
        Route::get('list','Admins\ReturnController@return_marked_list')->name('list');
        Route::post('marked/status','Admins\ReturnController@return_marked_status')->name('marked.status');
        Route::post('marked/status/single','Admins\ReturnController@return_marked_single_status')->name('marked.status.single');
        Route::get('confirmed','Admins\ReturnController@return_confirmed_view')->name('confirmed');
        Route::get('confirmed/list','Admins\ReturnController@return_confirmed_list')->name('confirmed.list');
        Route::post('confirmed/search','Admins\ReturnController@return_confirmed_search')->name('confirmed.search');
        Route::prefix('create')->name('create.')->group(function(){
            Route::get('','Admins\ReturnController@return_create_index')->name('index');
            Route::get('shipment_details','Admins\ReturnController@get_shipment_details')->name('shipment_details');
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
        });

        Route::prefix('receive')->name('receive.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@receive_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminCargoController@receive_shipment_details')->name('shipment_details');
            Route::post('short_received', 'Admins\AdminCargoController@receive_short_received')->name('short_received');
            Route::post('', 'Admins\AdminCargoController@receive_store')->name('store');
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
            Route::get('delivery_notes_list', 'Admins\AdminFinanceController@outstanding_sdn_delivery_notes_list')->name('delivery_notes_list');
            Route::post('reconcile_delivery_notes', 'Admins\AdminFinanceController@outstanding_sdn_reconcile_delivery_notes')->name('reconcile_delivery_notes');
            Route::get('export_to_excel', 'Admins\AdminFinanceController@outstanding_sdn_export_to_excel')->name('export_to_excel');
        });

        Route::prefix('outstanding_shipments')->name('outstanding_shipments.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@outstanding_shipments_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@outstanding_shipments_list')->name('list');
            Route::put('resolved', 'Admins\AdminFinanceController@outstanding_shipments_resolved')->name('resolved');
            Route::put('adjust_in_payment', 'Admins\AdminFinanceController@outstanding_shipments_adjust_in_payment')->name('adjust_in_payment');
        });

        Route::prefix('change_shipment_amount')->name('change_shipment_amount.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@change_shipment_amount_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminFinanceController@change_shipment_amount_shipment_details')->name('shipment_details');
            Route::post('', 'Admins\AdminFinanceController@change_shipment_amount_store')->name('store');
        });

        Route::prefix('make_payments')->name('make_payments.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@make_payments_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@make_payments_list')->name('list');
            Route::post('delivered_shipments', 'Admins\AdminFinanceController@make_payments_delivered_shipments')->name('delivered_shipments');
            Route::post('returned_shipments', 'Admins\AdminFinanceController@make_payments_returned_shipments')->name('returned_shipments');
            Route::post('adjusted_shipments', 'Admins\AdminFinanceController@make_payments_adjusted_shipments')->name('adjusted_shipments');
            Route::post('shipment_details', 'Admins\AdminFinanceController@make_payments_shipment_details')->name('shipment_details');
            Route::get('shipment_list', 'Admins\AdminFinanceController@make_payments_shipment_list')->name('shipment_list');
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
        });
        Route::prefix('pickup_note')->name('pickup_note.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@pickup_note_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@pickup_note_list')->name('list');
        });
        Route::prefix('cargo_received')->name('cargo_received.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@cargo_received_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@cargo_received_list')->name('list');
        });
        Route::prefix('lead_time')->name('lead_time.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@lead_time_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@lead_time_list')->name('list');
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
            Route::get('download', 'Admins\AdminReportsController@daily_pickup_sales_download')->name('download');
        });
        Route::prefix('customer_sales')->name('customer_sales.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@customer_sales_index')->name('index');
            Route::post('export_to_excel', 'Admins\AdminReportsController@customer_sales_export_to_excel')->name('export_to_excel');
            Route::get('download', 'Admins\AdminReportsController@customer_sales_download')->name('download');
        });
        Route::prefix('completed_delivery_notes')->name('completed_delivery_notes.')->group(function(){
            Route::get('','Admins\AdminReportsController@completed_delivery_notes_index')->name('index');
            Route::get('list','Admins\AdminReportsController@completed_delivery_notes_list')->name('list');
        });
        Route::prefix('customer_retention')->name('customer_retention.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@customer_retention_index')->name('index');
            Route::post('export_to_excel', 'Admins\AdminReportsController@customer_retention_export_to_excel')->name('export_to_excel');
            Route::get('download', 'Admins\AdminReportsController@customer_retention_download')->name('download');
        });
        Route::prefix('overall_sales')->name('overall_sales.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@overall_sales_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@overall_sales_list')->name('list');

        });

    });

    //Reports end
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
