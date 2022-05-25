<?php
use App\Http\Models\HR\Employee;
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

Route::get('payment_details/{id}/{id1}','TrackingController@payment_details')->name('payment_details');

Route::get('/', function () {
    return redirect()->route('cod.login');
});


Auth::routes();

Route::prefix('tracking')->name('tracking.')->group(function () {
    Route::get('{tracking_number?}', 'TrackingController@index')->name('index');
    Route::post('track', 'TrackingController@track')->name('track');
    Route::post('add','TrackingController@add_request')->name('add');
});

Route::prefix('shipment')->name('shipment.')->group(function () {
    Route::get('status/verify', 'ConsigneeShipmentResponseController@index')->name('status.verify');
    Route::get('status/{tracking_number?}/verify/{delivery_note_id?}', 'ConsigneeShipmentResponseController@index')->name('status.verify');
});

Route::prefix('cod')->name('cod.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('cod.login');
    });
    Route::get('404', 'Auth\LoginController@not_found')->name('404');
    Route::post('get/agreement','Shippers\ShipperDashboardController@get_agreement')->name('get_agreement');
    Route::post('rate/daily_visit','Shippers\ShipperDashboardController@rate_daily_visit')->name('rate_daily_visit');

    Route::get('/login','Auth\LoginController@showLoginForm')->name('login');
    Route::post('/login','Auth\LoginController@login')->name('login.submit');
//    Route::get('/register/','Auth\GetStartedController@index')->name('register');
//    Route::get('/get-started', 'Auth\GetStartedController@index')->name('getstarted');
//    Route::post('/get-started','Auth\GetStartedController@getstarted_submit')->name('getstarted');
    Route::get('/get-started-success','Auth\GetStartedController@getstarted_success')->name('getstarted.success');
    Route::get('/register/{lead_id?}','Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('/register','Auth\RegisterController@register')->name('register.submit');
    Route::get('/new/address','Auth\RegisterController@addressView')->name('new.address');
    Route::get('/new/bank','Auth\RegisterController@bankView')->name('new.bank');
    Route::get('/email/verified/{id?}','Auth\RegisterController@email_verified')->name('email.verified');
    Route::post('/salesPerson', 'Auth\RegisterController@sales_person')->name('salesPerson');
    Route::post('/territory', 'Auth\RegisterController@territory')->name('territory');
    Route::post('/area', 'Auth\RegisterController@area')->name('area');
    Route::post('update/agreement_status','Shippers\ShipperDashboardController@agreement_status')->name('update.agreement_status');

    Route::get('access_denied', 'Shippers\ShipperDashboardController@access_denied')->name('access_denied');
    Route::get('ledger', 'Shippers\ShipperDashboardController@ledger_index')->name('ledger');
    Route::get('ledger/list', 'Shippers\ShipperDashboardController@ledger_list')->name('ledger.list');

    Route::get('/welcome', 'Shippers\ShipperDashboardController@welcome_index')->name('welcome');
    Route::post('otp_verify', 'Shippers\ShipperDashboardController@opt_verify')->name('opt_verify');
    Route::get('opt_verify_close', 'Shippers\ShipperDashboardController@opt_verify_close')->name('opt_verify_close');
    Route::get('/dashboard', 'Shippers\ShipperDashboardController@orders_index')->name('dashboard');
    Route::get('/order/pending', 'Shippers\ShipperDashboardController@orderPending');
    Route::post('get_sub_segment', 'Auth\RegisterController@get_sub_segment')->name('get_sub_segment');
    
    Route::get('referral', 'Auth\RegisterController@referral_valid')->name('referral.valid');
   
    Route::prefix('orders')->name('orders.')->group(function(){
        Route::get('','Shippers\ShipperDashboardController@orders_index')->name('index');
        Route::get('list','Shippers\ShipperDashboardController@orders_list')->name('list');
        Route::post('search','Shippers\ShipperDashboardController@statistics_search')->name('search');
        Route::post('cancel','Shippers\ShipperDashboardController@order_cancel')->name('cancel');
        Route::post('cancel_all', 'Shippers\ShipperDashboardController@order_cancel_all')->name('cancel_all');
        Route::post('shipment_charges','Shippers\ShipperDashboardController@get_shipment_charges')->name('charges');
		Route::prefix('consolidate')->name('consolidate.')->group(function(){
            Route::post('shipment_info','Shippers\ShipperConsolidatedController@consolidate_shipment_info')->name('shipment_info');
            Route::post('submit','Shippers\ShipperConsolidatedController@consolidate_shipment_submit')->name('submit');
        });
    });
    Route::prefix('shipment')->name('shipment.')->group(function () {
        Route::prefix('book')->name('book.')->group(function () {
            Route::get('index', 'Shippers\ShipperShipmentBookController@corporate_index')->name('corporate.index');
            Route::post('corporate_store', 'Shippers\ShipperShipmentBookController@corporate_store')->name('corporate.store');
            Route::get('order_id', 'Shippers\ShipperShipmentBookController@order_id')->name('order_id');
            Route::get('restrict_order_id', 'Shippers\ShipperShipmentBookController@restrict_order_id')->name('restrict_order_id');
            Route::post('shipping_modes', 'Shippers\ShipperShipmentBookController@shipping_modes')->name('shipping_modes');
            Route::post('corporate_shipping_modes', 'Shippers\ShipperShipmentBookController@corporate_shipping_modes')->name('corporate_shipping_modes');
            Route::post('corporate_min_chargeable_weight', 'Shippers\ShipperShipmentBookController@corporate_min_chargeable_weight')->name('corporate_min_chargeable_weight');
            Route::post('print_air_waybill', 'Shippers\ShipperShipmentBookController@print_air_waybill')->name('print_air_waybill');
            Route::post('corporate_invoice', 'Shippers\ShipperShipmentBookController@corporate_invoice')->name('corporate_invoice');
            Route::post('check', 'Shippers\ShipperShipmentBookController@check')->name('check');
            Route::post('shipment_check', 'Shippers\ShipperShipmentBookController@shipment_check')->name('shipment_check');
            Route::get('get_consignee_infos', 'Shippers\ShipperShipmentBookController@get_consignee_infos')->name('get_consignee_infos');
            Route::post('get_consignee_info', 'Shippers\ShipperShipmentBookController@get_consignee_info')->name('get_consignee_info');
            Route::post('get_ftl_info', 'Shippers\ShipperShipmentBookController@get_ftl_info')->name('get_ftl_info');
            Route::post('check_cod_cap_zone_classes', 'Shippers\ShipperShipmentBookController@check_cod_cap_zone_classes')->name('check_cod_cap_zone_classes');
            Route::post('check_consignee_return_ratio', 'Shippers\ShipperShipmentBookController@check_consignee_return_ratio')->name('check_consignee_return_ratio');
            Route::post('check_shipment_allowed_city', 'Shippers\ShipperShipmentBookController@check_shipment_allowed_city')->name('check_shipment_allowed_city');
            

            Route::prefix('excel')->name('excel_')->group(function () {
                Route::get('', 'Shippers\ShipperShipmentBookController@excel_index')->name('index');
                Route::post('', 'Shippers\ShipperShipmentBookController@excel_store')->name('store');
            });
            Route::prefix('corporate_excel')->name('corporate_excel_')->group(function () {
                Route::get('', 'Shippers\ShipperShipmentBookController@corporate_excel_index')->name('index');
                Route::post('', 'Shippers\ShipperShipmentBookController@corporate_excel_store')->name('store');
                Route::get('/index', 'Shippers\ShipperShipmentBookController@corporate_excel_distribution_index')->name('distribution');
                Route::post('/store', 'Shippers\ShipperShipmentBookController@corporate_excel_distribution_store')->name('distribution.store');
            });
            Route::prefix('international')->name('international.')->group(function () {
                Route::get('', 'Shippers\ShipperInternationalShipmentBookController@index')->name('index');
                Route::post('', 'Shippers\ShipperInternationalShipmentBookController@store')->name('store');
                Route::prefix('excel')->name('excel_')->group(function () {
                    Route::get('', 'Shippers\ShipperInternationalShipmentBookController@excel_index')->name('index');
                    Route::post('', 'Shippers\ShipperInternationalShipmentBookController@excel_store')->name('store');
                });
            });


            Route::prefix('return_address')->name('return_address.')->group(function () {
                Route::prefix('excel')->name('excel.')->group(function () {
                    Route::get('', 'Shippers\ShipmentReturnAddressController@excel_index')->name('index');
                    Route::post('', 'Shippers\ShipmentReturnAddressController@excel_store')->name('store');
                });
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
            Route::post('print_receiving_sheet_and_air_waybill', 'Shippers\ShipperReceivingSheetController@print_receiving_sheet_and_air_waybill')->name('print_receiving_sheet_and_air_waybill');
            Route::post('cn/info','Shippers\ShipperReceivingSheetController@update_cn_info')->name('cn.info');
            Route::post('cn/update','Shippers\ShipperReceivingSheetController@update_consignee_info_and_special_instructions')->name('cn.update');

            Route::prefix('shipments')->name('shipments.')->group(function () {
                Route::get('', 'Shippers\ShipperReceivingSheetController@shipments_index')->name('index');
                Route::get('list', 'Shippers\ShipperReceivingSheetController@shipments_list')->name('list');
            });
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

        Route::prefix('list')->name('list.')->group(function () {
            Route::get('', 'Shippers\ShipperShipmentBookController@shipments_list_index')->name('index');
            Route::post('', 'Shippers\ShipperShipmentBookController@shipments_list_store')->name('store');
        });

        Route::prefix('verify')->name('verify.')->group(function () {
            Route::get('', 'Shippers\ShipperShipmentBookController@shipments_verify_index')->name('index');
            Route::post('', 'Shippers\ShipperShipmentBookController@shipments_verify_store')->name('store');
        });

        Route::prefix('origin')->name('origin.')->group(function(){
            Route::get('', 'Shippers\ShipmentOriginChangeController@shipments_origin_index')->name('index');
            Route::post('store', 'Shippers\ShipmentOriginChangeController@shipments_origin_store')->name('store');
        });

        Route::prefix('return_address_change')->name('return_address_change.')->group(function () {
            Route::get('', 'Shippers\ShipmentReturnAddressController@return_address_change_excel_index')->name('index');
            Route::post('', 'Shippers\ShipmentReturnAddressController@return_address_change_excel_store')->name('store');
        });

        Route::prefix('telenor')->name('telenor.')->group(function(){
            Route::prefix('other_courier')->name('other_courier.')->group(function() {
                Route::get('', 'Shippers\TelenorOtherCourierController@index')->name('index');
                Route::post('store', 'Shippers\TelenorOtherCourierController@store')->name('store');
            });
        });

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
    Route::prefix('order')->name('order.')->group(function () {
        Route::get('{order_id?}', 'Shippers\ShipperTrackingController@order_index')->name('index');
        Route::post('order_track', 'Shippers\ShipperTrackingController@order_track')->name('track');
    });
    Route::prefix('packaging')->name('packaging.')->group(function (){
        Route::prefix('requests')->name('requests.')->group(function (){
            Route::get('','Shippers\ShipperPackagingMaterialController@packaging_request')->name('index');
            Route::get('list','Shippers\ShipperPackagingMaterialController@packaging_request_list')->name('list');
            Route::post('details','Shippers\ShipperPackagingMaterialController@packaging_request_details')->name('details');
            Route::post('sizes','Shippers\ShipperPackagingMaterialController@packaging_request_sizes')->name('sizes');
            Route::post('submit','Shippers\ShipperPackagingMaterialController@packaging_request_submit')->name('submit');
            Route::post('cancel','Shippers\ShipperPackagingMaterialController@packaging_request_cancel')->name('cancel');

            Route::get('categories','Shippers\ShipperPackagingMaterialController@select_categories')->name('categories');
            Route::get('category/{id}','Shippers\ShipperPackagingMaterialController@category_products')->name('category');
            Route::get('product/{id}','Shippers\ShipperPackagingMaterialController@product_details')->name('product');
            Route::post('get_charges','Shippers\ShipperPackagingMaterialController@get_charges')->name('get_charges');
            Route::post('add_to_cart','Shippers\ShipperPackagingMaterialController@add_to_cart')->name('add_to_cart');
            Route::post('get_cart_count','Shippers\ShipperPackagingMaterialController@cart_count')->name('get_cart_count');
            Route::get('checkout','Shippers\ShipperPackagingMaterialController@checkout')->name('checkout');
            Route::post('remove_product','Shippers\ShipperPackagingMaterialController@remove_product')->name('remove_product');
            
            Route::prefix('cart')->name('cart.')->group(function (){
                Route::get('','Shippers\ShipperPackagingMaterialController@packaging_request_cart_index')->name('index');
                Route::post('details','Shippers\ShipperPackagingMaterialController@packaging_request_cart_details')->name('details');
            });
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
            Route::post('consignee', 'Shippers\ShipperReturnController@blacklist_search_consignee')->name('consignee');
        });
        Route::prefix('reattempt_history')->name('reattempt_history.')->group(function (){
            Route::get('','Shippers\ShipperReturnController@return_reattempt_history_index')->name('index');
            Route::get('list','Shippers\ShipperReturnController@return_reattempt_history_list')->name('list');
        });
        Route::prefix('confirmed')->name('confirmed.')->group(function (){
            Route::get('','Shippers\ShipperReturnController@return_confirmed_index')->name('index');
            Route::get('list','Shippers\ShipperReturnController@return_list')->name('list');
        });
        Route::prefix('sheet')->name('sheet.')->group(function (){
            Route::prefix('pending')->name('pending.')->group(function (){
                Route::get('','Shippers\ShipperReturnController@return_sheet_pending_index')->name('index');
                Route::get('list','Shippers\ShipperReturnController@return_sheet_pending_list')->name('list');
            });
            Route::prefix('receive')->name('receive.')->group(function (){
                Route::get('','Shippers\ShipperReturnController@return_sheet_receive_index')->name('index');
                Route::post('shipment_info','Shippers\ShipperReturnController@return_sheet_receive_shipment_info')->name('shipment_info');
                Route::post('submit','Shippers\ShipperReturnController@return_sheet_receive_submit')->name('submit');
            });
            Route::prefix('history')->name('history.')->group(function (){
                Route::get('','Shippers\ShipperReturnController@return_sheet_history_index')->name('index');
                Route::get('list','Shippers\ShipperReturnController@return_sheet_history_list')->name('list');
            });
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

            Route::prefix('reconcile_through_receiving_sheet')->name('reconcile_through_receiving_sheet.')->group(function () {
                Route::get('', 'Shippers\ShipperFinanceController@payments_reconcile_through_receiving_sheet_index')->name('index');
                Route::get('list', 'Shippers\ShipperFinanceController@payments_reconcile_through_receiving_sheet_list')->name('list');
            });
        });
        Route::prefix('invoice')->name('invoice.')->group(function () {
            Route::get('', 'Shippers\ShipperFinanceController@invoice_index')->name('index');
            Route::get('list', 'Shippers\ShipperFinanceController@invoice_list')->name('list');
            Route::post('detail_print', 'Shippers\ShipperFinanceController@invoices_detail_print')->name('detail_print');
            Route::get('export_to_excel', 'Shippers\ShipperFinanceController@invoices_export_to_excel')->name('export_to_excel');
            Route::get('reimbursement/export_to_excel', 'Shippers\ShipperFinanceController@reimbursement_invoices_export_to_excel')->name('reimbursement.export_to_excel');
            Route::put('email_reminder', 'Shippers\ShipperFinanceController@invoices_email_reminder')->name('email_reminder');
            Route::post('print_origin_wise', 'Shippers\ShipperFinanceController@invoices_print_origin_wise')->name('print_origin_wise');
            Route::post('print', 'Shippers\ShipperFinanceController@corporate_invoice_print')->name('invoices_print');

            Route::prefix('reimbursement')->name('reimbursement.')->group(function () {
                Route::post('detail_print', 'Shippers\ShipperFinanceController@reimbursement_invoices_print')->name('detail_print');
                Route::post('print', 'Shippers\ShipperFinanceController@invoice_reimbursement_print')->name('invoices_print');
            });

        });
    });

    Route::prefix('reports')->name('reports.')->group(function (){
        Route::prefix('qsr')->name('qsr.')->group(function (){
            Route::get('','Shippers\ShipperReportsController@qsr_index')->name('index');
            Route::get('list','Shippers\ShipperReportsController@qsr_list')->name('list');
        });
        Route::prefix('sales')->name('sales.')->group(function (){
            Route::get('','Shippers\ShipperReportsController@sales_index')->name('index');
            Route::post('list','Shippers\ShipperReportsController@sales_list')->name('list');

            Route::prefix('telenor')->name('telenor.')->group(function (){
                Route::get('','Shippers\ShipperReportsController@sales_telenor_index')->name('index');
                Route::get('list','Shippers\ShipperReportsController@sales_telenor_list')->name('list');
            });
        });
        Route::prefix('summary')->name('summary.')->group(function (){
            Route::get('','Shippers\ShipperReportsController@summary_index')->name('index');
            Route::get('list','Shippers\ShipperReportsController@summary_list')->name('list');
            Route::post('data','Shippers\ShipperReportsController@summary_data')->name('data');
        });

        Route::prefix('adjustments')->name('adjustments.')->group(function (){
            Route::get('', 'Shippers\ShipperReportsController@adjustments_index')->name('index');
            Route::get('list', 'Shippers\ShipperReportsController@adjustments_list')->name('list');
        });

        Route::prefix('weight_reconciliation')->name('weight_reconciliation.')->group(function (){
            Route::get('', 'Shippers\ShipperReportsController@weight_reconciliation_index')->name('index');
            Route::post('list', 'Shippers\ShipperReportsController@weight_reconciliation_list')->name('list');
        });

        

        Route::prefix('delivery_and_return')->name('delivery_and_return.')->group(function (){
            Route::get('','Shippers\ShipperReportsController@delivery_and_return_index')->name('index');
        });
        Route::prefix('confirmation_pending_report')->name('confirmation_pending_report.')->group(function (){
            Route::get('', 'Shippers\ShipperReportsController@confirmation_shipments_index')->name('index');
            Route::get('list', 'Shippers\ShipperReportsController@confirmation_shipments_list')->name('list');
        });
        Route::prefix('daraz_mis')->name('daraz_mis.')->group(function (){
            Route::get('','Shippers\ShipperReportsController@daraz_mis_index')->name('index');
            Route::get('list','Shippers\ShipperReportsController@daraz_mis_list')->name('list');
        });
    });

    Route::prefix('rates')->name('rates.')->group(function (){
        Route::prefix('view')->name('view.')->group(function (){
            Route::get('','Shippers\ShipperDashboardController@view_rates_index')->name('index');
        });
    });

    Route::get('/logout','Auth\LoginController@logout')->name('logout');
    Route::get('/register/user/success','Auth\RegisterController@register_success');
    Route::post('/logout','Auth\LoginController@logout')->name('logout');
    Route::get('/register/name/match/{name}','Auth\RegisterController@checkCompanyName');
    Route::get('/register/email/match/{email}/{id}','Auth\RegisterController@checkCompanyEmail')->name('check.email');
    Route::get('/register/name/match/{name}/{id}','Auth\RegisterController@checkCompanyNameProfile')->name('check.name');
    Route::get('terms_and_conditions/{token}/{id}/accept','ShipperAgreementController@accept')->name('terms.accept');
    Route::get('terms_and_conditions/{token}/{id}/download','ShipperAgreementController@crf_download')->name('terms.download');
    Route::get('/terms/success','Auth\RegisterController@register_success')->name('terms.success');

    //user profile
    Route::get('/profile','Shippers\ShipperDashboardController@userProfile')->name('edit.profile');
    Route::post('updateprofile','Shippers\ShipperDashboardController@updateProfile')->name('update.profile');
    Route::post('update/profile/password','Shippers\ShipperDashboardController@update_profile_password')->name('update.profile.password');
    Route::get('getpickups','Shippers\ShipperDashboardController@getPickups')->name('get.pickups');
    Route::get('getbanks','Shippers\ShipperDashboardController@getBanks')->name('get.banks');
    Route::post('default_bank','Shippers\ShipperDashboardController@updateDefaultBanks')->name('default.bank');
    Route::post('add_bank','Shippers\ShipperDashboardController@addBank')->name('add.bank');
    Route::post('verify_pincode','Shippers\ShipperDashboardController@verifyPincode')->name('verify.pin.code');
    Route::post('changepickupstatus','Shippers\ShipperDashboardController@pickupStatusChange')->name('change.pickup.status');
    Route::post('addpickup','Shippers\ShipperDashboardController@addPickup')->name('add.pickup');
    Route::post('editpickup','Shippers\ShipperDashboardController@editPickup')->name('edit.pickup');
    Route::post('updateprofile','Shippers\ShipperDashboardController@updateProfile')->name('update.profile');
    Route::post('edit/emails','Shippers\ShipperDashboardController@edit_notification_emails')->name('edit.emails');
    Route::post('add/emails','Shippers\ShipperDashboardController@add_notification_emails')->name('add.emails');
    Route::get('contacts','Shippers\ShipperDashboardController@contacts')->name('contacts');
    Route::post('update_invoice_sort','Shippers\ShipperDashboardController@update_invoice_sort')->name('update_invoice_sort');
    Route::get('/phone_unique', 'Shippers\ShipperDashboardController@shipper_phone_unique')->name('profile.shipper_phone_unique');

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
            Route::post('re_open', 'Shippers\ShipperCRMController@re_open_request')->name('re_open');
            Route::post('/lost/claim', 'Shippers\ShipperCRMController@lost_claim')->name('lost.claim');
            Route::post('feedback', 'Shippers\ShipperCRMController@customer_feedback')->name('feedback');

        });
        Route::prefix('feedback')->name('feedback.')->group(function(){
            Route::post('add', 'Shippers\ShipperCRMController@add_feedback')->name('add');
        });
        Route::prefix('comment')->name('comment.')->group(function(){
            Route::post('add', 'Shippers\ShipperCRMController@add_comment')->name('add');
            Route::post('get', 'Shippers\ShipperCRMController@get_latest_comment')->name('get');
        });

    });

    Route::prefix('cancelled_shipments')->name('cancelled_shipments.')->group(function (){
        Route::get('','Shippers\ShipperShipmentCancelController@index')->name('index');
        Route::get('list', 'Shippers\ShipperShipmentCancelController@list')->name('list');
        Route::put('revert', 'Shippers\ShipperShipmentCancelController@revert')->name('revert');
    });
    Route::prefix('intercept')->name('intercept.')->group(function (){
        Route::get('/{row_id}','Shippers\ShipperInterceptReBookController@intercept_re_book_index')->name('index');
        Route::post('update','Shippers\ShipperInterceptReBookController@intercept_re_book_update')->name('update');
      
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::prefix('air_waybill_printing')->name('air_waybill_printing.')->group(function () {
            Route::get('', 'Shippers\ShipperGlobalSettingsController@air_waybill_printing_count_index')->name('index');
            Route::post('store', 'Shippers\ShipperGlobalSettingsController@air_waybill_printing_count_store')->name('store');
        });
        Route::prefix('logo')->name('logo.')->group(function () {
            Route::get('', 'Shippers\ShipperGlobalSettingsController@upload_logo_index')->name('index');
            Route::post('store', 'Shippers\ShipperGlobalSettingsController@upload_logo_submit')->name('upload');
            Route::post('remove', 'Shippers\ShipperGlobalSettingsController@remove_logo')->name('remove');
        });

        Route::prefix('shipping_information')->name('shipping_information.')->group(function () {
            Route::get('', 'Shippers\ShipperGlobalSettingsController@shipping_information_index')->name('index');
            Route::get('list', 'Shippers\ShipperGlobalSettingsController@shipping_information_list')->name('list');
            Route::post('add_iban', 'Shippers\ShipperGlobalSettingsController@shipping_information_add_iban')->name('add_iban');
            Route::get('bank_info','Shippers\ShipperGlobalSettingsController@shipping_information_bank_info')->name('bank_info');
        });
        Route::prefix('subscription')->name('subscription.')->group(function () {
            Route::get('', 'Shippers\ShipperGlobalSettingsController@subscription_index')->name('index');
            Route::post('store', 'Shippers\ShipperGlobalSettingsController@subscription_submit')->name('store');
        });

        Route::prefix('initial_charges_subscription')->name('initial_charges_subscription.')->group(function () {
            Route::get('', 'Shippers\ShipperGlobalSettingsController@initial_charges_subscription_index')->name('index');
            Route::post('store', 'Shippers\ShipperGlobalSettingsController@initial_charges_subscription_submit')->name('store');
        });

        Route::prefix('final_charges_subscription')->name('final_charges_subscription.')->group(function () {
            Route::get('', 'Shippers\ShipperGlobalSettingsController@final_charges_subscription_index')->name('index');
            Route::post('store', 'Shippers\ShipperGlobalSettingsController@final_charges_subscription_submit')->name('store');
        });

        Route::prefix('payment_subscription')->name('payment_subscription.')->group(function () {
            Route::get('', 'Shippers\ShipperGlobalSettingsController@payment_subscription_index')->name('index');
            Route::post('store', 'Shippers\ShipperGlobalSettingsController@payment_subscription_submit')->name('store');
        });

        Route::prefix('receiving_sheet')->name('receiving_sheet.')->group(function () {
            Route::get('', 'Shippers\ShipperGlobalSettingsController@receiving_sheet_description_index')->name('index');
            Route::post('store', 'Shippers\ShipperGlobalSettingsController@receiving_sheet_description_submit')->name('store');
        });

    });
	Route::prefix('consolidation')->name('consolidation.')->group(function () {
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('', 'Shippers\ShipperConsolidatedController@consolidation_history_index')->name('index');
            Route::get('list', 'Shippers\ShipperConsolidatedController@consolidation_history_list')->name('list');
        });
    });


    Route::prefix('pickup')->name('pickup.')->group(function () {
        Route::get('', 'Shippers\ShipperPickupController@pickup_index')->name('index');
        Route::get('list', 'Shippers\ShipperPickupController@pickup_list')->name('list');
        Route::post('shipments', 'Shippers\ShipperPickupController@shipments')->name('shipments');
        Route::post('add_remarks', 'Shippers\ShipperPickupController@add_remarks')->name('add_remarks');
        Route::post('cancel', 'Shippers\ShipperPickupController@cancel')->name('cancel');
        Route::post('renew', 'Shippers\ShipperPickupController@renew')->name('renew');
        Route::get('view_details', 'Shippers\ShipperPickupController@view_details')->name('view_details');
    });

    Route::prefix('multiple_pieces')->name('multiple_pieces.')->group(function (){
        Route::get('', 'Shippers\ShipperShipmentPieceController@hold_index')->name('index');
        Route::get('list', 'Shippers\ShipperShipmentPieceController@hold_list')->name('list');
        Route::post('single_piece','Shippers\ShipperShipmentPieceController@single_piece')->name('single_piece');
        Route::post('wait_remaining_pieces','Shippers\ShipperShipmentPieceController@wait_remaining_pieces')->name('wait_remaining_pieces');
        Route::post('return_back_to_shipper','Shippers\ShipperShipmentPieceController@return_back_to_shipper')->name('return_back_to_shipper');
        Route::prefix('resolved')->name('resolved.')->group(function (){
            Route::get('', 'Shippers\ShipperShipmentPieceController@resolved_index')->name('index');
            Route::get('list', 'Shippers\ShipperShipmentPieceController@resolved_list')->name('list');
        });

    });

    Route::prefix('telenor')->name('telenor.')->group(function () {
        Route::prefix('data_conversion')->name('data_conversion.')->group(function () {
            Route::get('', 'Shippers\ShipperTelenorController@data_conversion_index')->name('index');
            Route::post('', 'Shippers\ShipperTelenorController@data_conversion_store')->name('store');
        });
    });

    Route::prefix('quick_search')->name('quick_search.')->group(function(){
        Route::get('','Shippers\ShipperDashboardController@quick_search_index')->name('index');
        Route::get('list','Shippers\ShipperDashboardController@quick_search_list')->name('list');
    });
});
//Admin Routes Start
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.login');
    });

    Route::prefix('activity_trail')->name('activity_trail.')->group(function () {
        Route::get('', 'Admins\ActivityTrailController@activity_trail_index')->name('index');
        Route::get('list', 'Admins\ActivityTrailController@activity_trail_list')->name('list');
    });

    Route::get('/login','Auth\AdminLoginController@showLoginForm')->name('login');
    Route::post('/login','Auth\AdminLoginController@login')->name('login.submit');
    Route::post('/credentials', 'Auth\AdminLoginController@credentials')->name('login.credentials');
    Route::post('/verify_otp', 'Auth\AdminLoginController@verify_otp')->name('login.verify_otp');
    Route::get('access_denied', 'Admins\AdminController@access_denied')->name('access_denied');
    Route::post('save_coordinates', 'Admins\AdminController@save_coordinates')->name('save_coordinates');
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('', 'Admins\AdminDashboardController@index')->name('index');
        Route::post('search','Admins\AdminDashboardController@statistics_search')->name('search');
        Route::get('incoming_list','Admins\AdminDashboardController@incoming_list')->name('incoming_list');
        Route::get('delivered_returned_list','Admins\AdminDashboardController@delivered_returned_list')->name('delivered_returned_list');
        Route::get('outgoing_top_customers_list','Admins\AdminDashboardController@outgoing_top_customers_list')->name('outgoing_top_customers_list');
        Route::get('incoming_weight_range_list','Admins\AdminDashboardController@incoming_weight_range_list')->name('incoming_weight_range_list');
        Route::get('outgoing_weight_range_list','Admins\AdminDashboardController@outgoing_weight_range_list')->name('outgoing_weight_range_list');
        Route::get('operation_forecast_search','Admins\AdminDashboardController@operation_forecast_search')->name('operation_forecast_search');
        Route::get('admin_profile', 'Admins\AdminDashboardController@admin_profile')->name('admin_profile');
        Route::get('edit_profile', 'Admins\AdminDashboardController@edit_profile')->name('edit_profile');
        //Search Sonic
//        Route::get('search_sonic', 'Admins\AdminDashboardController@search_sonic')->name('search_sonic');
        
         //commision dashboard routes
    // Route::prefix('commission')->name('commission.')->group(function () {
       // Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('commission/User', 'Admins\AdminCommissionController@dashboard_userwise_index')->name('userwise');
            Route::post('commission/list', 'Admins\AdminCommissionController@dashboard_userwise_list')->name('list');
            Route::post('commission/data', 'Admins\AdminCommissionController@dashboard_userwise_data')->name('userwise.commission.data');
            Route::get('overall', 'Admins\AdminCommissionController@dashboard_overall_index')->name('overall');
       // });
   // });
        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('index', 'Dashboard\BusinessProjectionRetentionController@dashboard')->name('index');
            Route::get('list', 'Dashboard\BusinessProjectionRetentionController@dashboard_list')->name('list');
            Route::post('update_reason','Dashboard\BusinessProjectionRetentionController@update_reason')->name('update_reason');
        });

        Route::get('overall/commission', 'Admins\AdminCommissionController@overall_commission_dashboard')->name('overall.commission');
        Route::post('overall/commission/list', 'Admins\AdminCommissionController@overall_commission_dashboard_list')->name('overall.commission.list');
        Route::post('overall/commission/data', 'Admins\AdminCommissionController@overall_commission_dashboard_data')->name('overall.commission.data');
    });

    Route::prefix('ftl')->name('ftl.')->group(function (){
        Route::prefix('request')->name('request.')->group(function (){
            Route::get('/','Admins\FTLController@ftl_request_index')->name('index');
            Route::get('/list','Admins\FTLController@ftl_request_list')->name('list');
            Route::get('/view/{id}','Admins\FTLController@ftl_request_view')->name('view');
            Route::post('/add','Admins\FTLController@ftl_request_add')->name('add');
            Route::prefix('comment')->name('comment.')->group(function (){
                Route::post('/add','Admins\FTLController@ftl_request_add_comment')->name('add');
                Route::post('/get','Admins\FTLController@ftl_request_get_comments')->name('get');
            });
            Route::prefix('update')->name('update.')->group(function (){
                Route::post('/shipper/{id}','Admins\FTLController@ftl_request_update_shipper')->name('shipper');
                Route::post('/status/{id}','Admins\FTLController@ftl_request_update_status')->name('status');
            });
        });
    });

    Route::prefix('operation_forecasting')->name('operation_forecasting.')->group(function () {
        Route::prefix('incoming')->name('incoming.')->group(function () {
            Route::get('{from?}/{to?}/{service_type_id?}/{hub?}/{status?}', 'Admins\AdminDashboardController@shipments_list')->name('shipments_list');
        });
        Route::prefix('outgoing')->name('outgoing.')->group(function () {
            Route::get('{from?}/{to?}/{customer_id?}', 'Admins\AdminDashboardController@outgoing_shipments_list')->name('shipments_list');
        });
    });


    Route::get('update/profile/password','Admins\AdminDashboardController@update_profile_password')->name('update.profile.password');
    Route::post('update/profile/password/submit','Admins\AdminDashboardController@update_profile_password_submit')->name('update.profile.password.submit');
    Route::post('update/profile/submit','Admins\AdminDashboardController@edit_profile_submit')->name('update.profile.submit');
    Route::prefix('update_one_time_profile')->name('update_one_time_profile.')->group(function () {
        Route::get('','Admins\AdminDashboardController@get_one_time_profile')->name('index');
        Route::get('check','Admins\AdminDashboardController@check_profile')->name('check');
        Route::post('submit','Admins\AdminDashboardController@update_one_time_profile')->name('submit');
    });



    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('', 'Admins\OrderManagementController@index')->name('index');
        Route::get('list', 'Admins\OrderManagementController@orders_list')->name('list');
        Route::post('shipment_charges','Admins\OrderManagementController@get_shipment_charges')->name('charges');
        Route::post('shipper_recall','Admins\OrderManagementController@shipper_recall')->name('shipper_recall');
        Route::get('shipment_print_status', 'Admins\OrderManagementController@shipment_print_status')->name('shipment_print_status');
        Route::post('telenor_shipments_arrival','Admins\OrderManagementController@telenor_shipments_arrival')->name('telenor_shipments_arrival');
        Route::post('foodpanda_shipments_arrival','Admins\OrderManagementController@foodpanda_shipments_arrival')->name('foodpanda_shipments_arrival');

        Route::prefix('self_collection')->name('self_collection.')->group(function () {
            Route::get('', 'Admins\OrderManagementController@self_collection_index')->name('index');
            Route::get('list', 'Admins\OrderManagementController@self_collection_list')->name('list');
        });
    });
    Route::get('/order/pending', 'Admins\AdminDashboardController@orderPending');
    Route::prefix('accounts')->name('accounts.')->group(function(){
        Route::get('pending', 'Admins\AdminDashboardController@pendingAccountsList')->name('pending');
        Route::post('pending/ajax', 'Admins\AdminDashboardController@pendingAccountListAjax')->name('pending.ajax');
        Route::get('active', 'Admins\AdminDashboardController@activeAccountsList')->name('active');
        Route::post('active/ajax', 'Admins\AdminDashboardController@activeAccountListAjax')->name('active.ajax');
        Route::post('cancelation-days', 'Admins\AdminDashboardController@auto_cancelation_days')->name('auto_cancelation_days');
        Route::get('block', 'Admins\AdminDashboardController@blockAccountsList')->name('block');
        Route::get('block/ajax', 'Admins\AdminDashboardController@blockAccountListAjax')->name('block.ajax');
        Route::post('status/block','Admins\AdminDashboardController@UserStatusBlock')->name('status.block');
        Route::post('status/change','Admins\AdminDashboardController@UserStatusChange')->name('status.change');
        Route::put('status', 'Admins\AdminDashboardController@UserStatus')->name('status');
        Route::post('tag/submit','Admins\AdminDashboardController@tagSubmit')->name('tag.submit');
        Route::post('tag/submit/bulk','Admins\AdminDashboardController@tagSubmitBulk')->name('tag.submit.bulk');
        Route::post('set_segment/bulk','Admins\AdminDashboardController@setSegmentBulk')->name('set.segment_bulk');
        Route::post('reject/submit','Admins\AdminDashboardController@rejectReasonSubmit')->name('rejectreason.submit');
        Route::post('auto_shipment_cancel_days/submit','Admins\AdminShipmentCancelController@auto_shipment_cancel_days')->name('auto_shipment_cancel_days.submit');
        Route::post('kam_poc_ref_tag/submit','Admins\AdminDashboardController@kam_poc_ref_tag')->name('kam_poc_ref_tag.submit');
        Route::post('rate_type/submit','Admins\AdminCorporateAccountsController@rate_type_submit')->name('rate_type.submit');
        Route::post('/add_territory', 'Admins\AdminDashboardController@add_territory')->name('add_territory');
        Route::post('/add_segments', 'Admins\AdminDashboardController@add_segments')->name('add_segments');
        Route::get('active_today', 'Admins\AdminDashboardController@todayActiveAccountsList')->name('active.today');
        Route::post('active_today/ajax', 'Admins\AdminDashboardController@todayActiveAccountListAjax')->name('active.today.ajax');
        Route::get('kam_poc_ref_tag/info','Admins\AdminDashboardController@kam_poc_ref_tag_info')->name('kam_poc_ref_tag.info');
        Route::post('kam_poc_ref_tag/remove','Admins\AdminDashboardController@kam_poc_ref_tag_remove')->name('kam_poc_ref_tag.remove');
        Route::post('restrict_order_id/info','Admins\AdminDashboardController@restrict_order_id_info')->name('restrict_order_id.info');
        Route::post('restrict_order_id/submit','Admins\AdminDashboardController@restrict_order_id_submit')->name('restrict_order_id.submit');
        Route::post('/add_retag_territory', 'Admins\AdminDashboardController@add_retag_territory')->name('add_retag_territory');

        Route::get('duplicate/info','Admins\AdminDashboardController@duplicate_info')->name('duplicate.info');
        Route::prefix('payment_cycle')->name('payment_cycle.')->group(function(){
            Route::get('info','Admins\AdminDashboardController@payment_cycle_info')->name('info');
            Route::post('submit', 'Admins\AdminDashboardController@payment_cycle_submit')->name('submit');
        });

        //user profile
        Route::get('/{id}/view','Admins\AdminDashboardController@userProfile')->name('view.profile');
        Route::post('/updateprofile','Admins\AdminDashboardController@updateProfile')->name('update.profile');
        Route::get('getpickups','Admins\AdminDashboardController@getPickups')->name('get.pickups');
        Route::post('/updatebankinfo','Admins\AdminDashboardController@updateBankInfo')->name('update.bank');
        Route::post('edit/emails','Admins\AdminDashboardController@edit_notification_emails')->name('edit.emails');
        Route::post('add/emails','Admins\AdminDashboardController@add_notification_emails')->name('add.emails');
        Route::get('/{id}/documents','Admins\AdminDashboardController@userDocuments')->name('documents');
        Route::get('/{id}/{check}/{pdf}/documents','Admins\AdminDashboardController@viewUserDocuments')->name('documents.view');
        Route::get('/{id}/{approve}/{reason}/approve/documents','Admins\AdminDashboardController@approveDocuments')->name('documents.approve');
        Route::post('//documents/upload','Admins\AdminDashboardController@uploadDocuments')->name('documents.upload');
        Route::post('/documents/confirm', 'Admins\AdminDashboardController@userDocumentsConfirm')->name('documents.confirm');

        //my route
        Route::post('/documents/edit', 'Admins\AdminDashboardController@userDocumentsEdit')->name('documents.edit');

        Route::prefix('sister_account')->name('sister_account.')->group(function(){
            Route::get('{id}/add/','Admins\AdminDashboardController@add_sister_account_view')->name('add.account');
            Route::post('add/submit','Admins\AdminDashboardController@add_sister_account_submit')->name('add.submit');
            Route::get('{id}','Admins\AdminDashboardController@edit_sister_account_view')->name('edit.index');
            Route::post('edit/submit','Admins\AdminDashboardController@edit_sister_account_submit')->name('edit.submit');
            Route::post('account/info','Admins\AdminDashboardController@get_account_info')->name('info');
        });

        Route::prefix('merged_account')->name('merged_account.')->group(function(){
            Route::get('','Admins\AdminDashboardController@merged_accounts_index')->name('index');
            Route::get('list','Admins\AdminDashboardController@merged_accounts_list')->name('list');
            Route::post('info','Admins\AdminDashboardController@merged_accounts_info')->name('info');
            Route::prefix('mapping')->name('mapping.')->group(function() {
                Route::post('mapping/info', 'Admins\AdminDashboardController@merged_accounts_mapping_info')->name('info');
                Route::post('submit', 'Admins\AdminDashboardController@merged_accounts_mapping_submit')->name('submit');
            });
        });

        Route::prefix('warehousing')->name('warehousing.')->group(function(){
            Route::post('active','Admins\AdminDashboardController@warehousing_active')->name('active');
            Route::post('inactive','Admins\AdminDashboardController@warehousing_inactive')->name('inactive');
        });

        Route::prefix('packaging')->name('packaging.')->group(function(){
            Route::post('invoice_log','Admins\AdminDashboardController@packaging_invoice_log')->name('invoice.log');
        });
    });

    Route::prefix('daily_visit')->name('daily_visit.')->group(function () {
        Route::get('', 'Admins\AdminDailyVisitController@daily_visit_index')->name('index');
        Route::post('store', 'Admins\AdminDailyVisitController@daily_visit_store')->name('store');
        Route::get('business_card/{business_card}', 'Admins\AdminDailyVisitController@business_card')->name('business_card');
        Route::get('location_photo/{location_photo}', 'Admins\AdminDailyVisitController@location_photo')->name('location_photo');
    });

    //Datatables data using ajax calls
    //add rates view
    Route::get('/accounts/{id}/add/rates','Admins\AdminDashboardController@addRatesView')->name('add.rates');
    Route::post('/accounts/{id}/add/rates','Admins\AdminDashboardController@addRates')->name('add.rates.submit');
    //edit rates
    Route::get('/accounts/{id}/edit/rates','Admins\AdminDashboardController@editRatesView')->name('edit.rates');
    Route::post('/edit/user_documents','Admins\AdminDashboardController@edit_rates_user_documents')->name('edit.user_documents');
    Route::put('/accounts/{id}/edit/rates','Admins\AdminDashboardController@editRates')->name('edit.rates.submit');
    Route::get('accounts/{id}/view_crf_agreement', 'ShipperAgreementController@view_crf_agreement')->name('accounts.view_crf_agreement');
    //
    Route::get('/accounts/{id}/view/rates/{date?}','Admins\AdminDashboardController@viewRates')->name('view.rates');
    Route::post('user_id','Admins\AdminDashboardController@rate_history_date')->name('view.user');
    //Route::post('/accounts/rates_history','Admins\AdminDashboardController@viewRatesHistory')->name('view.rates.history');
    Route::get('/accounts/{id}/add_contacts','Admins\AdminDashboardController@add_contacts')->name('accounts.add_contacts');
    Route::post('/accounts/add_contacts.store','Admins\AdminDashboardController@add_contacts_store')->name('accounts.add_contacts.store');



    Route::prefix('corporate')->name('corporate.')->group(function (){
        Route::get('{id}/add/rates/{rate_type_id?}','Admins\AdminCorporateAccountsController@add_rates_index')->name('add.rates');
        Route::post('{id}/add/rates','Admins\AdminCorporateAccountsController@add_rates_submit')->name('add.rates');
        Route::get('{id}/edit/rates','Admins\AdminCorporateAccountsController@edit_rates_index')->name('edit.rates');
        Route::post('/edit/user_documents','Admins\AdminDashboardController@edit_rates_user_documents')->name('edit.user_documents');
        Route::put('{id}/edit/rates','Admins\AdminCorporateAccountsController@edit_rates_submit')->name('edit.rates');
        Route::get('{id}/view/rates/{date?}','Admins\AdminCorporateAccountsController@view_rates_index')->name('view.rates');
        Route::post('reject/submit','Admins\AdminCorporateAccountsController@rejectReasonSubmit')->name('rejectreason.submit');
        Route::prefix('zone_wise')->name('zone_wise.')->group(function (){
            Route::post('{id}/add/rates','Admins\AdminCorporateAccountsController@add_rates_zone_wise_submit')->name('add.rates');
            Route::put('{id}/edit/rates','Admins\AdminCorporateAccountsController@edit_rates_zone_wise_submit')->name('edit.rates');
        });
        Route::prefix('default')->name('default.')->group(function (){
            Route::post('{id}/add/rates','Admins\AdminCorporateAccountsController@add_rates_default_submit')->name('add.rates');
            Route::get('{id}/edit/rates','Admins\AdminCorporateAccountsController@edit_rates_default')->name('edit.rates');
            Route::get('{id}/edit/rates/view','Admins\AdminCorporateAccountsController@edit_rates_default')->name('view.rates');
            Route::put('{id}/edit/rates/update','Admins\AdminCorporateAccountsController@edit_rates_default_submit')->name('edit.rates.submit');
            Route::post('check/corporate_rate_type','Admins\AdminCorporateAccountsController@check_corporate_rate_type')->name('rate_type');
            Route::post('{id}/add/rates/submit','Admins\AdminCorporateAccountsController@change_corporate_rate_type')->name('change_rate_type');
            Route::get('{id}/view/rates','Admins\AdminCorporateAccountsController@default_view_rates_index')->name('rates.view');
        });
        Route::prefix('reimbursement_setting')->name('reimbursement_setting.')->group(function (){
           Route::get('{id}','Admins\AdminCorporateAccountsController@corporate_reimbursement_setting')->name('index');
           Route::post('{id}','Admins\AdminCorporateAccountsController@corporate_reimbursement_setting_store')->name('store');
           Route::post('{id}/approve','Admins\AdminCorporateAccountsController@corporate_reimbursement_setting_approve')->name('approve');
           Route::post('{id}/reject','Admins\AdminCorporateAccountsController@corporate_reimbursement_setting_reject')->name('reject');
        });
    });
    //ajax request


    //new address
    Route::prefix('management')->name('management.')->group(function () {

        Route::get('/city', 'Admins\AdminDashboardController@cityView')->name('city.index');
        Route::get('/city/ajax', 'Admins\AdminDashboardController@cityListAjax')->name('city.ajax');
        Route::get('/city/form', 'Admins\AdminDashboardController@getCityForm')->name('city.form');
        Route::get('/international/city/form', 'Admins\AdminDashboardController@getInternationalCityForm')->name('international.city.form');
        Route::get('/city/{id}/edit/form', 'Admins\AdminDashboardController@getEditCityForm')->name('city.edit');
        Route::get('/international/city/{id}/edit/form', 'Admins\AdminDashboardController@getEditInternationalCityForm')->name('international.city.edit');
        Route::post('/city', 'Admins\AdminDashboardController@addCityHub')->name('city');
        Route::put('/city/{id}/edit/form', 'Admins\AdminDashboardController@updateCity')->name('city.edit');
        Route::put('/international/city/{id}/edit/form', 'Admins\AdminDashboardController@updateInternationalCity')->name('international.city.edit');
        Route::post('/international/city', 'Admins\AdminDashboardController@addInternationalCityHub')->name('international.city');
        Route::put('/city/status', 'Admins\AdminDashboardController@CityStatus')->name('city.status');
        Route::get('/city/{id}/status/ajax', 'Admins\AdminDashboardController@CityStatusCheck')->name('city.status.ajax');
        Route::get('', 'Admins\AdminDashboardController@walk_in_city_list')->name('city_list');
        Route::post('', 'Admins\AdminDashboardController@check_min_charges')->name('min_charges');
//        Route::post('shippingModesAjax', 'Admins\AdminDashboardController@modesAjax')->name('shippingModes.ajax');

        //Route
        Route::prefix('route')->name('route.')->group(function () {
            Route::get('/','Admins\AdminDashboardController@routeView')->name('index');
            Route::get('ajax', 'Admins\AdminDashboardController@routeListAjax')->name('ajax');
            Route::get('/add', 'Admins\AdminDashboardController@addRouteView')->name('add');
            Route::post('/add', 'Admins\AdminDashboardController@addRouteDetails')->name('add');
            Route::get('{id}/edit', 'Admins\AdminDashboardController@editRouteView')->name('edit');
            Route::put('{id}/edit', 'Admins\AdminDashboardController@editRouteDetails')->name('edit');
            Route::put('/status', 'Admins\AdminDashboardController@routeStatus')->name('status');
            Route::post('/assign_location', 'Admins\AdminDashboardController@assign_locations_submit')->name('assign_location');
            Route::post('/user_address', 'Admins\AdminDashboardController@user_address')->name('user_address');
            Route::post('/assign_locations_view', 'Admins\AdminDashboardController@assign_locations_view')->name('assign_locations_view');
            Route::post('/set_pickup_route', 'Admins\AdminDashboardController@set_as_pickup_route')->name('set_pickup_route');
        });
        Route::prefix('rider')->name('rider.')->group(function (){
           // Route::get('','Admins\AdminDashboardController@riderView')->name('index');
            Route::get('ajax', 'Admins\AdminDashboardController@riderListAjax')->name('ajax');
            Route::get('/add', 'Admins\AdminDashboardController@addRiderView')->name('add');
            Route::get('categoryAjax', 'Admins\AdminDashboardController@categoryListAjax')->name('category.ajax');
            Route::post('/add', 'Admins\AdminDashboardController@addRiderDetails')->name('add');
            Route::get('{id}/edit', 'Admins\AdminDashboardController@editRiderView')->name('edit');
            Route::put('{id}/edit', 'Admins\AdminDashboardController@editRiderDetails')->name('edit');
            Route::put('/status', 'Admins\AdminDashboardController@riderStatus')->name('status');
            Route::get('/phone_unique', 'Admins\AdminDashboardController@rider_phone_unique')->name('phone_unique');
        });
        Route::prefix('riders')->name('riders.')->group(function (){
            Route::get('/add/{type?}', 'Admins\RiderManagementController@addRiderView')->name('add');
            Route::get('categoryAjax', 'Admins\RiderManagementController@categoryListAjax')->name('category.ajax');
            Route::post('/add', 'Admins\RiderManagementController@addRiderDetails')->name('add');
            Route::post('rejoin', 'Admins\RiderManagementController@rejoin')->name('rejoin');
            Route::get('{id}/edit/{type?}', 'Admins\RiderManagementController@editRiderView')->name('edit');
            Route::put('{id}/edit', 'Admins\RiderManagementController@editRiderDetails')->name('edit');
            Route::put('/status', 'Admins\RiderManagementController@riderStatus')->name('status');
            Route::get('/phone_unique', 'Admins\RiderManagementController@rider_phone_unique')->name('phone_unique');
            Route::post('incentive', 'Admins\RiderManagementController@rider_incentive')->name('incentive');
            Route::post('permanent', 'Admins\RiderManagementController@rider_permanent')->name('permanent');
            Route::post('rider_blacklist', 'Admins\RiderManagementController@rider_blacklist')->name('rider_blacklist');
            Route::post('send_sms', 'Admins\RiderManagementController@send_sms')->name('send_sms');

            Route::prefix('permanent')->name('permanent.')->group(function (){
                Route::get('','Admins\RiderManagementController@permanent_index')->name('index');
                Route::get('list', 'Admins\RiderManagementController@permanent_list')->name('list');
            });

            Route::prefix('incentive')->name('incentive.')->group(function (){
                Route::get('','Admins\RiderManagementController@incentive_index')->name('index');
                Route::get('list', 'Admins\RiderManagementController@incentive_list')->name('list');
            });

            Route::prefix('blacklist')->name('blacklist.')->group(function (){
                Route::get('','Admins\RiderManagementController@blacklist_index')->name('index');
                Route::get('list', 'Admins\RiderManagementController@blacklist_list')->name('list');
            });

            Route::prefix('rider_request')->name('rider_request.')->group(function (){
                Route::get('','Admins\RiderManagementController@rider_request_index')->name('index');
                Route::get('list', 'Admins\RiderManagementController@rider_request_list')->name('list');
                Route::post('/approve', 'Admins\RiderManagementController@approveRider')->name('approve');
            });

            Route::prefix('sms_history')->name('sms_history.')->group(function (){
                Route::get('','Admins\RiderManagementController@sms_history_index')->name('index');
                Route::get('list', 'Admins\RiderManagementController@sms_history_list')->name('list');
                Route::get('riders_name', 'Admins\RiderManagementController@all_riders')->name('riders_name');

            });


        });

        Route::prefix('zonal')->name('zonal.')->group(function () {
            Route::get('', 'Admins\AdminZonalManagementController@index')->name('index');
            Route::get('/list', 'Admins\AdminZonalManagementController@list')->name('list');

            Route::prefix('add')->name('add.')->group(function () {
                Route::get('', 'Admins\AdminZonalManagementController@add_index')->name('index');
                Route::post('', 'Admins\AdminZonalManagementController@add_store')->name('store');
                Route::get('/international', 'Admins\AdminZonalManagementController@add_international_index')->name('international.index');
                Route::post('/international', 'Admins\AdminZonalManagementController@add_international_store')->name('international.store');
            });

            Route::prefix('update/{id}')->name('update.')->group(function () {
                Route::get('', 'Admins\AdminZonalManagementController@update_index')->name('index');
                Route::post('', 'Admins\AdminZonalManagementController@update_store')->name('store');
            });

            Route::prefix('update/international/{id}')->name('update.international.')->group(function () {
                Route::get('', 'Admins\AdminZonalManagementController@update_international_index')->name('index');
                Route::post('', 'Admins\AdminZonalManagementController@update_international_store')->name('store');
            });

            Route::post('view_cities', 'Admins\AdminZonalManagementController@view_cities')->name('view_cities');
            Route::post('status_update', 'Admins\AdminZonalManagementController@zonal_status_update')->name('status_update');
        });

        Route::prefix('territory')->name('territory.')->group(function () {
            Route::get('', 'Admins\AdminTerritoryController@index')->name('index');
            Route::get('/list', 'Admins\AdminTerritoryController@list')->name('list');
            Route::get('/add', 'Admins\AdminTerritoryController@add')->name('add');
            Route::post('/store', 'Admins\AdminTerritoryController@store')->name('store');
            Route::post('/ajax', 'Admins\AdminTerritoryController@edit_territory_ajax')->name('edit');
            Route::post('/disable_territory', 'Admins\AdminTerritoryController@disable_territory')->name('disable_territory');
            Route::post('/enable_territory', 'Admins\AdminTerritoryController@enable_territory')->name('enable_territory');
            Route::put('{id}/update', 'Admins\AdminTerritoryController@update')->name('update');

        });
        Route::prefix('area')->name('area.')->group(function () {
            Route::get('', 'Admins\AdminTerritoryController@area_index')->name('index');
            Route::get('/list', 'Admins\AdminTerritoryController@area_list')->name('list');
            Route::get('/add', 'Admins\AdminTerritoryController@area_add')->name('add');
            Route::post('/store', 'Admins\AdminTerritoryController@area_store')->name('store');
            Route::post('/ajax', 'Admins\AdminTerritoryController@area_edit')->name('edit');
            Route::post('/disable_area_status', 'Admins\AdminTerritoryController@disable_area_status')->name('disable_area_status');
            Route::post('/enable_area_status', 'Admins\AdminTerritoryController@enable_area_status')->name('enable_area_status');
            Route::put('{id}/update', 'Admins\AdminTerritoryController@area_update')->name('update');
            Route::post('tag', 'Admins\AdminTerritoryController@area_tag')->name('tag');

        });

        Route::post('/city/osa_list', 'Admins\AdminDashboardController@osa_list')->name('city.osa_list');



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

            Route::prefix('try_and_buy')->name('try_and_buy.')->group(function () {
                Route::post('item_details', 'Admins\AdminPickupsController@try_and_buy_item_details')->name('item_details');
                Route::post('shipment_details', 'Admins\AdminPickupsController@receive_try_and_buy_shipment_details')->name('shipment_details');
            });
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
            Route::post('delivered', 'Admins\AdminPickupsController@bookedvsreceived_delivered_list')->name('delivered');
            Route::post('returned', 'Admins\AdminPickupsController@bookedvsreceived_returned_list')->name('returned');
        });

        Route::prefix('rider')->name('rider.')->group(function () {
            Route::get('', 'Rider\RiderPickupsController@pickups_index')->name('index');
            Route::get('list', 'Rider\RiderPickupsController@pickups_list')->name('list');
            //  Route::get('v2_list', 'Rider\RiderPickupsController@pickups_list_v2')->name('v2_list');
            Route::get('shipments', 'Rider\RiderPickupsController@pickups_shipments')->name('shipments');

            Route::prefix('action_log')->name('action_log.')->group(function () {
                Route::get('', 'Rider\RiderPickupsController@pickups_action_log_index')->name('index');
                Route::get('list', 'Rider\RiderPickupsController@pickups_action_log_list')->name('list');
                // Route::get('v2_list', 'Rider\RiderPickupsController@pickups_action_log_list_v2')->name('v2_list');
            });
        });

        Route::prefix('quick_arrival_of_shipments')->name('quick_arrival_of_shipments.')->group(function () {
            Route::get('', 'Admins\AdminPickupsController@quick_arrival_of_shipments_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminPickupsController@quick_arrival_of_shipments_shipment_details')->name('shipment_details');
            Route::post('shipment_remove', 'Admins\AdminPickupsController@quick_arrival_of_shipments_remove')->name('shipment_remove');
            Route::post('', 'Admins\AdminPickupsController@quick_arrival_of_shipments_store')->name('store');
        });
    });

    Route::prefix('multiple_pieces')->name('multiple_pieces.')->group(function (){
        Route::post('upload_attachment', 'Admins\AdminShipmentPieceController@upload_attachment')->name('upload_attachment');
        Route::get('view_attachment/{id}', 'Admins\AdminShipmentPieceController@view_attachment')->name('view_attachment');
        Route::prefix('hold')->name('hold.')->group(function () {
            Route::get('', 'Admins\AdminShipmentPieceController@hold_index')->name('index');
            Route::get('list', 'Admins\AdminShipmentPieceController@hold_list')->name('list');
            Route::post('single_piece','Admins\AdminShipmentPieceController@single_piece')->name('single_piece');
            Route::post('wait_remaining_pieces','Admins\AdminShipmentPieceController@wait_remaining_pieces')->name('wait_remaining_pieces');
            Route::post('return_back_to_shipper','Admins\AdminShipmentPieceController@return_back_to_shipper')->name('return_back_to_shipper');
            Route::post('return_note_create', 'Admins\AdminShipmentPieceController@return_note_create')->name('return_note_create');
            Route::post('return_note_print', 'Admins\AdminShipmentPieceController@return_note_print')->name('return_note_print');
            Route::post('single_piece_bulk','Admins\AdminShipmentPieceController@single_piece_bulk')->name('single_piece_bulk');
            Route::post('wait_remaining_pieces_bulk','Admins\AdminShipmentPieceController@wait_remaining_pieces_bulk')->name('wait_remaining_pieces_bulk');
            Route::post('return_back_to_shipper_bulk','Admins\AdminShipmentPieceController@return_back_to_shipper_bulk')->name('return_back_to_shipper_bulk');
        });
        Route::prefix('add')->name('add.')->group(function () {
            Route::get('', 'Admins\AdminShipmentPieceController@hold_add_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminShipmentPieceController@hold_shipment_details')->name('shipment_details');
            Route::post('submit', 'Admins\AdminShipmentPieceController@hold_shipment_submit')->name('submit');
        });

        Route::prefix('resolved')->name('resolved.')->group(function () {
            Route::get('', 'Admins\AdminShipmentPieceController@hold_resolved_index')->name('index');
            Route::get('list', 'Admins\AdminShipmentPieceController@hold_resolved_list')->name('list');
        });

    });
    Route::prefix('v2_pickups')->name('v2_pickups.')->group(function () {
        Route::prefix('rider')->name('rider.')->group(function () {
            Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@v2_pickups_index')->name('index');
            Route::get('v2_list', 'Admins\V2Pickup\V2AdminPickupsController@pickups_list_v2')->name('list');
            Route::get('shipments', 'Admins\V2Pickup\V2AdminPickupsController@pickups_shipments')->name('shipments');
        });
        Route::prefix('action_log')->name('action_log.')->group(function () {
            Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@pickups_action_log_index_v2')->name('index');
            Route::get('v2_list', 'Admins\V2Pickup\V2AdminPickupsController@pickups_action_log_list_v2')->name('list');
        });
        Route::prefix('un_assigned')->name('un_assigned.')->group(function () {
            Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@unassigned_index')->name('index');
            Route::get('/list', 'Admins\V2Pickup\V2AdminPickupsController@unassigned_list')->name('list');
        });
        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@pending_index')->name('index');
            Route::get('/list', 'Admins\V2Pickup\V2AdminPickupsController@pending_list')->name('list');
            Route::put('assign', 'Admins\V2Pickup\V2AdminPickupsController@pending_assign')->name('assign');
            Route::put('update', 'Admins\V2Pickup\V2AdminPickupsController@pending_update')->name('update');
            Route::post('bookings/all','Admins\V2Pickup\V2AdminPickupsController@pending_all_bookings')->name('bookings.all');
            Route::post('bookings/received','Admins\V2Pickup\V2AdminPickupsController@pending_received_bookings')->name('bookings.received');
            Route::post('print', 'Admins\V2Pickup\V2AdminPickupsController@assigned_print')->name('print');
            Route::post('status/reminder/update', 'Admins\V2Pickup\V2AdminPickupsController@pending_reminder')->name('status.reminder.update');

        });
    // Receiving Sheet Rout
        Route::prefix('rider_receiving')->name('rider_receiving.')->group(function () {
            Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@rider_receiving_index')->name('index');
            Route::get('/list', 'Admins\V2Pickup\V2AdminPickupsController@rider_receiving_list')->name('list');
            Route::post('check_pickup', 'Admins\V2Pickup\V2AdminPickupsController@rider_receiving_check_pickup')->name('check_pickup');
            Route::post('print', 'Admins\V2Pickup\V2AdminPickupsController@rider_receiving_print')->name('print');
            Route::post('total_shipments', 'Admins\V2Pickup\V2AdminPickupsController@total_shipments')->name('total_shipments');
            Route::post('arrived_shipments', 'Admins\V2Pickup\V2AdminPickupsController@arrived_shipments')->name('arrived_shipments');
            Route::prefix('dws')->name('dws.')->group(function () {
                Route::get('', 'Admins\V2Pickup\DWSController@rider_receiving_index')->name('index');
                Route::get('/list', 'Admins\V2Pickup\DWSController@rider_receiving_list')->name('list');
                Route::post('total_shipments', 'Admins\V2Pickup\DWSController@total_dws_shipments')->name('total_shipments');
            });
        });
        // End

        Route::prefix('arrival')->name('arrival.')->group(function () {
            Route::prefix('bulk')->name('bulk.')->group(function () {
                Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@arrival_bulk_index')->name('index');
                Route::post('shipment_details', 'Admins\V2Pickup\V2AdminPickupsController@arrival_bulk_shipment_details')->name('shipment_details');
                Route::prefix('try_and_buy')->name('try_and_buy.')->group(function () {
                    Route::post('item_details', 'Admins\AdminPickupsController@try_and_buy_item_details')->name('item_details');
                    Route::post('shipment_details', 'Admins\V2Pickup\V2AdminPickupsController@arrival_try_and_buy_shipment_details')->name('shipment_details');
                });
                Route::prefix('piece')->name('piece.')->group(function () {
                    Route::post('piece_details', 'Admins\V2Pickup\V2AdminPickupsController@arrival_piece_details')->name('piece_details');
                    Route::post('shipment_details', 'Admins\V2Pickup\V2AdminPickupsController@arrival_piece_shipment_details')->name('shipment_details');
                });
                Route::post('store', 'Admins\V2Pickup\V2AdminPickupsController@bulk_arrival_submit')->name('store');
            });

            Route::prefix('individual')->name('individual.')->group(function () {
                Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@arrival_individual_index')->name('index');
                Route::post('shipment_details', 'Admins\V2Pickup\V2AdminPickupsController@arrival_individual_shipment_details')->name('shipment_details');
                Route::post('shipment_remove', 'Admins\V2Pickup\V2AdminPickupsController@arrival_individual_shipment_remove')->name('shipment_remove');
                Route::prefix('try_and_buy')->name('try_and_buy.')->group(function () {
                    Route::post('item_details', 'Admins\AdminPickupsController@try_and_buy_item_details')->name('item_details');
                    Route::post('shipment_details', 'Admins\V2Pickup\V2AdminPickupsController@arrival_try_and_buy_shipment_details')->name('shipment_details');
                });
                Route::post('store', 'Admins\V2Pickup\V2AdminPickupsController@individual_arrival_submit')->name('store');
            });
        });

        Route::prefix('arrival_service')->name('arrival_service.')->group(function () {
            Route::prefix('service')->name('service.')->group(function () {
                Route::get('', 'Admins\V2Pickup\V2AdminArrivalServiceController@arrival_service_index')->name('index');
                Route::post('shipment_details', 'Admins\V2Pickup\V2AdminArrivalServiceController@arrival_service_details')->name('shipment_details');
                Route::post('store', 'Admins\V2Pickup\V2AdminArrivalServiceController@service_arrival_submit')->name('store');
            });

        });

        Route::prefix('pickup_route')->name('pickup_route.')->group(function () {
            Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@pickup_route_index')->name('index');
            Route::get('/list', 'Admins\V2Pickup\V2AdminPickupsController@pickup_route_list')->name('list');
            Route::post('/ajax', 'Admins\V2Pickup\V2AdminPickupsController@edit_route_ajax')->name('edit_ajax');
            //Route::post('store', 'Admins\V2Pickup\V2AdminArrivalServiceController@service_arrival_submit')->name('store');
        });

        Route::prefix('rider_tracking')->name('rider_tracking.')->group(function () {
            Route::get('', 'Admins\V2Pickup\V2RiderTrackingController@rider_tracking_index')->name('index');
            Route::get('by_rider', 'Admins\V2Pickup\V2RiderTrackingController@rider_tracking_by_rider')->name('by_rider');
            Route::get('by_city', 'Admins\V2Pickup\V2RiderTrackingController@rider_tracking_by_city')->name('by_city');
        });

    });

    Route::prefix('delivery')->name('delivery.')->group(function(){
        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('','Admins\DeliveryController@pending_delivery_index')->name('index');
            Route::get('list','Admins\DeliveryController@pending_list')->name('list');
        });
        Route::prefix('note')->name('note.')->group(function () {
            Route::get('','Admins\DeliveryController@delivery_note_index')->name('index');
            Route::post('check/rider/dncc_status','Admins\DeliveryController@check_rider_dncc_status')->name('rider_dncc_status');
            Route::post('shipment/info','Admins\DeliveryController@get_shipment_details')->name('shipment.info');
            Route::post('shipment/piece_details', 'Admins\DeliveryController@get_piece_details')->name('shipment.piece_details');
            Route::post('create','Admins\DeliveryController@create_delivery_note')->name('create');
            Route::post('rider_check','Admins\DeliveryController@delivery_note_rider_check')->name('rider_check');
			Route::post('consolidation_check','Admins\DeliveryController@note_consolidation_check')->name('consolidation_check');
			Route::post('operation_riders','Admins\DeliveryController@operation_riders')->name('operation_riders');
			Route::get('request','Admins\DeliveryController@request_index')->name('request_index');
			Route::get('request/list','Admins\DeliveryController@request_list')->name('request_list');
			Route::post('request/submit','Admins\DeliveryController@request_submit')->name('request_submit');
			Route::post('request/info','Admins\DeliveryController@delivery_note_info')->name('request.info');
			Route::post('request/approve','Admins\DeliveryController@request_approve')->name('request.approve');
			Route::post('otp/generate','Admins\DeliveryController@delivery_note_otp_generation')->name('otp.generate');
			Route::post('otp/verify','Admins\DeliveryController@delivery_note_otp_verification')->name('otp.verify');
        });
        Route::prefix('cash_collection')->name('cash_collection.')->group(function (){
            Route::prefix('pending')->name('pending.')->group(function () {
                Route::get('', 'Admins\DeliveryController@pending_cash_collection_index')->name('index');
                Route::get('list', 'Admins\DeliveryController@pending_cash_collection_list')->name('list');
                Route::post('collect', 'Admins\DeliveryController@pending_cash_collect')->name('collect');
                Route::post('all','Admins\DeliveryController@pending_cash_collect_all')->name('all');
                Route::post('shipments','Admins\DeliveryController@cash_collection_shipments')->name('shipments');
                Route::post('shipments/delivered','Admins\DeliveryController@cash_collection_shipments_delivered')->name('shipments.delivered');
                Route::post('shipments/ccd_slip','Admins\DeliveryController@cash_collection_shipments_ccd_slip')->name('shipments.ccd_slip');
                Route::post('shipments/upload_ccd_receipt','Admins\DeliveryController@cash_collection_upload_receipt')->name('shipments.upload_ccd_receipt');
                //HBL Konnect
                Route::post('transactions/information','Admins\DeliveryController@hbl_konnect_transactions_information')->name('transactions.information');
                //HBL Konnect

            });
            Route::prefix('retail')->name('retail.')->group(function(){
                Route::get('', 'Admins\Retail\RetailCashCollectionController@retail_index')->name('index');
                Route::get('list', 'Admins\Retail\RetailCashCollectionController@retail_list')->name('list');

                Route::prefix('pending')->name('pending.')->group(function(){
                    Route::post('shipments','Admins\Retail\RetailCashCollectionController@number_of_shipments')->name('shipments');
                    Route::post('shipments/delivered','Admins\Retail\RetailCashCollectionController@shipments_delivered')->name('shipments.delivered');
                    Route::post('collect', 'Admins\Retail\RetailCashCollectionController@pending_cash_collect')->name('collect');
                    Route::post('all','Admins\Retail\RetailCashCollectionController@pending_cash_collect_all')->name('collect_all');
                });

            });
        });
        Route::prefix('receive')->name('receive.')->group(function (){
            Route::get('','Admins\DeliveryController@delivery_note_receive_index')->name('index');
            Route::get('list','Admins\DeliveryController@receive_deliveries_list')->name('list');
            Route::post('shipments','Admins\DeliveryController@receive_delivery_shipments')->name('shipments');
            Route::post('receive_shipments_delivered','Admins\DeliveryController@receive_shipments_delivered')->name('receive_shipments_delivered');
            Route::post('receive_shipments_undelivered','Admins\DeliveryController@receive_shipments_undelivered')->name('receive_shipments_undelivered');
            Route::post('receive_shipments_pending','Admins\DeliveryController@receive_shipments_pending')->name('receive_shipments_pending');
            Route::get('tracking/search','Admins\DeliveryController@receive_delivery_search')->name('tracking.search');
            Route::get('{id}/update','Admins\DeliveryController@receive_delivery_update')->name('update');
            Route::get('{id}/update/list','Admins\DeliveryController@receive_delivery_notes_list')->name('update.list');
            Route::post('update/remove','Admins\DeliveryController@receive_delivery_remove')->name('update.remove');
            Route::post('update/remove_bulk','Admins\DeliveryController@receive_delivery_remove_bulk')->name('update.remove.bulk');
            Route::post('print','Admins\DeliveryController@received_print')->name('print');
            Route::get('{id}/status','Admins\DeliveryController@receive_delivery_status_view')->name('status');
            Route::post('password/check','Admins\DeliveryController@receive_delivery_password_check')->name('password.check');
            Route::post('add/status','Admins\DeliveryController@receive_delivery_status_submit')->name('add.status');
            Route::post('add/status/all','Admins\DeliveryController@receive_delivery_status_submit_all')->name('add.status.all');
            Route::get('{id}/add/list','Admins\DeliveryController@receive_delivery_status_list')->name('add.list');
            Route::post('reason','Admins\DeliveryController@receive_delivery_reason')->name('reason');
            Route::post('reason_all','Admins\DeliveryController@receive_delivery_reason_all')->name('reason_all');
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

            Route::post('distribution','Admins\DeliveryController@receive_delivery_get_distribution')->name('distribution');
            Route::put('distribution/submit','Admins\DeliveryController@receive_delivery_distribution_submit')->name('distribution.submit');


            Route::get('{id}/status/verify','Admins\DeliveryController@receive_delivery_note_verify_view')->name('status.verify');
            Route::get('{id}/verify/status/list','Admins\DeliveryController@receive_delivery_verify_status_list')->name('verify.status.list');
            Route::put('verify/status/submit','Admins\DeliveryController@receive_delivery_verify_status_submit')->name('verify.status.submit');
            Route::post('dncc/print','Admins\DeliveryController@dncc_print')->name('dncc.print');
            Route::post('undelivered/print','Admins\DeliveryController@dncc_undelivered_print')->name('undelivered.print');
            Route::post('reassign_rider','Admins\DeliveryController@reassign_rider')->name('reassign_rider');
            Route::post('/add/tracking_number','Admins\DeliveryController@add_shipments_in_receive_deliveries')->name('add.shipments');
            Route::post('/upload_pod','Admins\DeliveryController@upload_pod')->name('upload_pod');

            Route::post('fake_status_shipments','Admins\DeliveryController@fake_status_shipments')->name('fake_status_shipments');


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


            Route::prefix('retail')->name('retail.')->group(function() {
                Route::get('', 'Admins\Retail\RetailCompletedDeliveries@index')->name('index');
                Route::get('list', 'Admins\Retail\RetailCompletedDeliveries@list')->name('list');
                Route::post('shipments/delivered','Admins\Retail\RetailCompletedDeliveries@shipments_delivered')->name('shipments.delivered');

                Route::post('deposit/pncc','Admins\Retail\RetailCompletedDeliveries@completed_deliveries_selected_pncc')->name('deposit.pncc');
                Route::get('pncc/list','Admins\Retail\RetailCompletedDeliveries@get_sdn_list')->name('pncc.list');

                Route::get('sdn/create','Admins\Retail\RetailCompletedDeliveries@create_sdn_view')->name('sdn.create');
                Route::post('sdn/create','Admins\Retail\RetailCompletedDeliveries@create_sdn_submit')->name('sdn.create.submit');

            });
        });
        Route::prefix('sdn')->name('sdn.')->group(function (){
            Route::get('','Admins\DeliveryController@sdn_view')->name('index');
            Route::get('list','Admins\DeliveryController@sdn_list')->name('list');
            Route::post('get/petty_cash_statements','Admins\DeliveryController@get_petty_cash_statements')->name('get.petty_cash_statements');
            Route::post('get/adjustment_reference','Admins\DeliveryController@get_adjustment_reference')->name('get.adjustment_reference');
            Route::post('back_to_deposit','Admins\DeliveryController@back_to_deposit')->name('back_to_deposit');
            Route::post('closed','Admins\DeliveryController@closed')->name('closed');
            Route::post('bulk_closed','Admins\DeliveryController@bulk_closed')->name('bulk_closed');
            Route::post('dn','Admins\DeliveryController@sdn_dncc_list')->name('dn');
            Route::get('{id}/details','Admins\DeliveryController@sdn_details')->name('details');
            Route::get('{id}/ajax','Admins\DeliveryController@sdn_details_ajax')->name('ajax');
            Route::post('slip','Admins\DeliveryController@sdn_deposit_slip')->name('slip');
            Route::post('print','Admins\DeliveryController@sdn_deposit_slip_print')->name('print');
            Route::post('dncc/print','Admins\DeliveryController@sdn_dncc_print')->name('dncc.print');
            Route::post('shipments','Admins\DeliveryController@sdn_delivered_shipments')->name('shipments');
            Route::post('slip_view','Admins\DeliveryController@sdn_slip_view')->name('slip_view');
            Route::post('adjustment/add','Admins\DeliveryController@sdn_adjustment_add')->name('adjustment.add');
            Route::get('petty_cash_detail','Admins\DeliveryController@sdn_petty_cash_detail')->name('petty_cash_detail');

            Route::post('status_logs','Admins\DeliveryController@sdn_status_logs')->name('status_logs');

            Route::prefix('retail')->name('retail.')->group(function() {
                Route::get('{id}/details','Admins\Retail\RetailCompletedDeliveries@sdn_details')->name('details');
                Route::get('{id}/ajax','Admins\Retail\RetailCompletedDeliveries@sdn_details_ajax')->name('ajax');
            });

            Route::prefix('dncc')->name('dncc.')->group(function() {
                Route::post('add','Admins\DeliveryController@add_dncc')->name('add');
                Route::post('get/dncc','Admins\DeliveryController@get_dncc_to_add')->name('get.add');
                Route::post('remove','Admins\DeliveryController@remove_dncc')->name('remove');
                Route::post('get/dncc/remove','Admins\DeliveryController@get_dncc_to_remove')->name('get.remove');
            });

            Route::prefix('pncc')->name('pncc.')->group(function() {
                Route::post('add','Admins\DeliveryController@add_pncc')->name('add');
                Route::post('get/pncc','Admins\DeliveryController@get_pncc_to_add')->name('get.add');
                Route::post('remove','Admins\DeliveryController@remove_pncc')->name('remove');
                Route::post('get/pncc/remove','Admins\DeliveryController@get_pncc_to_remove')->name('get.remove');
            });
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
        Route::prefix('quick_receiving')->name('quick_receiving.')->group(function (){
            Route::get('','Admins\DeliveryController@quick_receiving_delivery_index')->name('index');
            Route::post('','Admins\DeliveryController@quick_receiving_submit')->name('submit');
            Route::post('track_delivery_note','Admins\DeliveryController@quick_receiving_track_delivery_note')->name('track_delivery_note');
            Route::post('track_tracking_number','Admins\DeliveryController@quick_receiving_track_tracking_number')->name('track_tracking_number');
        });

        Route::prefix('signature')->name('signature.')->group(function () {
            Route::get('', 'Admins\DeliveryController@signature_index')->name('index');
            Route::get('list', 'Admins\DeliveryController@signature_list')->name('list');

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
            Route::prefix('log')->name('log.')->group(function () {
                Route::get('', 'Admins\DeliveryController@log_fake_statuses_index')->name('index');
                Route::post('store', 'Admins\DeliveryController@log_fake_statuses_store')->name('store');
            });
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
        Route::post('list','Admins\ReturnController@return_marked_list')->name('list');
        Route::post('confirm/status','Admins\ReturnController@return_confirm_status')->name('confirm.status');
        Route::post('reattempt/status','Admins\ReturnController@return_reattempt_status')->name('reattempt.status');

        Route::post('marked/status/single','Admins\ReturnController@return_marked_single_status')->name('marked.status.single');
        Route::get('confirmed','Admins\ReturnController@return_confirmed_view')->name('confirmed');
        Route::get('confirmed/list','Admins\ReturnController@return_confirmed_list')->name('confirmed.list');
        Route::post('confirmed/search','Admins\ReturnController@return_confirmed_search')->name('confirmed.search');
        Route::post('excel/store','Admins\ReturnController@excel_store')->name('excel.store');
        Route::post('excel/assign_agent_excel','Admins\ReturnController@assign_agent_excel')->name('excel.assign_agent_excel');
        Route::post('assign/agent','Admins\ReturnController@assign_agent')->name('assign.agent');
        Route::post('unassign/agent','Admins\ReturnController@unassign_agent')->name('unassign.agent');
        Route::get('/confirmation_pending/sms','Admins\ReturnController@confirmation_pending_sms_index')->name('confirmation_pending_sms');
        Route::get('/confirmation_pending/sms/list','Admins\ReturnController@confirmation_pending_sms_list')->name('confirmation_pending_sms_list');

        Route::post('marked/self_collection','Admins\ReturnController@change_status_to_self_collection')->name('marked.self_collection');
        Route::post('edit/estimated_charges','Admins\ReturnController@update_estimated_charges')->name('edit.estimated_charges');
        Route::post('rcp_sms','Admins\ReturnController@manual_rcp_sms')->name('rcp_sms');

        Route::prefix('confirmed')->name('confirmed.')->group(function (){
            Route::post('revert','Admins\ReturnController@return_confirmed_revert')->name('revert');
            Route::post('revert/status','Admins\ReturnController@return_revert_status')->name('revert.status');
            Route::post('excel/store','Admins\ReturnController@excel_store_revert')->name('excel.store');
        });

        Route::prefix('create')->name('create.')->group(function(){
            Route::get('','Admins\ReturnController@return_create_index')->name('index');
            Route::get('riders/by_hub', 'Admins\ReturnController@get_riders_by_hub')->name('riders.hub');
            Route::post('shipment_details','Admins\ReturnController@get_shipment_details')->name('shipment_details');
            Route::post('shipment/piece_details', 'Admins\ReturnController@get_piece_details')->name('shipment.piece_details');

            Route::post('note/submit','Admins\ReturnController@return_note_create')->name('note.submit');
        });
        Route::prefix('receive')->name('receive.')->group(function (){
            Route::get('','Admins\ReturnController@return_receive_deliveries_view')->name('index');
            Route::get('list','Admins\ReturnController@return_receive_deliveries_list')->name('list');
            Route::get('{id}/update','Admins\ReturnController@return_receive_update')->name('update');
            Route::get('{id}/update/list','Admins\ReturnController@return_receive_update_list')->name('update.list');
            Route::get('update/remove','Admins\ReturnController@return_receive_update_remove')->name('update.remove');
            Route::get('{id}/status','Admins\ReturnController@return_receive_status')->name('status');
            Route::post('status/submit','Admins\ReturnController@receive_return_status_submit')->name('status.submit');
            Route::post('status/submit_all','Admins\ReturnController@receive_return_status_submit_all')->name('status.submit_all');
            Route::post('status/delivered','Admins\ReturnController@return_status_delivered')->name('status.delivered');
            Route::get('status/list','Admins\ReturnController@return_receive_status_list')->name('status.list');
            Route::post('reason','Admins\ReturnController@receive_return_reason')->name('reason');
            Route::post('rn.print','Admins\ReturnController@rrd_print')->name('rn.print');
            Route::post('shipments','Admins\ReturnController@receive_return_shipments')->name('shipments');
            Route::post('upload_image','Admins\ReturnController@receive_return_note_image_upload')->name('upload_image');
            Route::post('undelivered/print','Admins\ReturnController@return_undelivered_print')->name('undelivered.print');

        });
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('', 'Admins\ReturnController@history_index')->name('index');
            Route::get('list', 'Admins\ReturnController@history_list')->name('list');
            Route::post('shipments', 'Admins\ReturnController@history_shipments')->name('shipments');
            Route::post('delivered_shipments', 'Admins\ReturnController@history_delivered_shipments')->name('delivered_shipments');
            Route::post('get_images', 'Admins\ReturnController@history_get_images')->name('get_images');
            Route::post('delete_image', 'Admins\ReturnController@history_delete_image')->name('delete_image');
            Route::post('delete_lastimage', 'Admins\ReturnController@history_delete_lastimage')->name('delete_lastimage');

        });
        Route::prefix('cx_sales')->name('cx_sales.')->group(function () {
            Route::get('', 'Admins\ReturnController@cx_sales_index')->name('index');
            Route::get('list', 'Admins\ReturnController@cx_sales_list')->name('list');

        });
        Route::prefix('return_deliveries')->name('return_deliveries.')->group(function () {
            Route::get('', 'Admins\ReturnController@return_deliveries_index')->name('index');
            Route::get('list', 'Admins\ReturnController@return_deliveries_list')->name('list');
            Route::get('app_shipment_list', 'Admins\ReturnController@return_deliveries_app_shipments_list')->name('app_shipment_list');
            Route::get('dbf_shipment_list', 'Admins\ReturnController@return_deliveries_dbf_shipments_list')->name('dbf_shipment_list');

        });

        Route::prefix('rcp_agent')->name('rcp_agent.')->group(function () {
            Route::get('', 'Admins\ReturnController@rcp_agent_index')->name('index');
            Route::get('list', 'Admins\ReturnController@rcp_agent_list')->name('list');
            Route::post('data', 'Admins\ReturnController@rcp_agent_data')->name('data');

        });
        Route::prefix('revert')->name('revert.')->group(function () {
            Route::get('', 'Admins\ReturnController@return_revert_index')->name('index');
            Route::post('shipment_details', 'Admins\ReturnController@return_revert_shipment_details')->name('shipment_details');
            Route::post('submit', 'Admins\ReturnController@return_revert_submit')->name('submit');
        });
    });
    Route::prefix('debriefing')->name('debriefing.')->group(function (){
        Route::prefix('supervisor')->name('supervisor.')->group(function (){
            Route::get('','Admins\LastMileDebriefingController@supervisor_view')->name('index');
            Route::get('list','Admins\LastMileDebriefingController@supervisor_list')->name('list');
            Route::post('agents','Admins\LastMileDebriefingController@supervisor_agents')->name('agents');
            Route::post('assign_agents','Admins\LastMileDebriefingController@supervisor_assign_agents')->name('assign_agents');
            Route::post('get_undelivered_shipments','Admins\LastMileDebriefingController@get_undelivered_shipments')->name('get_undelivered_shipments');
            Route::post('send_sms', 'Admins\LastMileDebriefingController@send_sms_to_undelivered_shipments')->name('send_sms');

        });
        Route::prefix('agents_call_monitoring')->name('agents_call_monitoring.')->group(function (){
            Route::get('','Admins\LastMileDebriefingController@agents_call_monitoring_view')->name('index');
            Route::get('list','Admins\LastMileDebriefingController@agents_call_monitoring_list')->name('list');
        });
        Route::prefix('caller_agent')->name('caller_agent.')->group(function (){
            Route::get('','Admins\LastMileDebriefingController@caller_agent_view')->name('index');
            Route::post('next','Admins\LastMileDebriefingController@caller_agent_next')->name('next');
            Route::post('skip','Admins\LastMileDebriefingController@caller_agent_skip')->name('skip');
            Route::post('follow_up/{id}','Admins\LastMileDebriefingController@caller_agent_follow_up')->name('follow_up');
            Route::post('start','Admins\LastMileDebriefingController@caller_agent_start')->name('start');
            Route::post('break','Admins\LastMileDebriefingController@caller_agent_break')->name('break');
            Route::post('end','Admins\LastMileDebriefingController@caller_agent_end')->name('end');
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
            Route::post('short_received_shipments', 'Admins\AdminCargoController@in_transit_short_received_shipments')->name('short_received_shipments');
            Route::post('lost', 'Admins\AdminCargoController@in_transit_lost')->name('lost');
            Route::post('send_details', 'Admins\AdminCargoController@send_from_junction')->name('send_details');
            Route::post('send_from_junction', 'Admins\AdminCargoController@in_transit_send_from_junction')->name('send_from_junction');
        });

        Route::prefix('receive')->name('receive.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@receive_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminCargoController@receive_shipment_details')->name('shipment_details');
            Route::post('short_received', 'Admins\AdminCargoController@receive_short_received')->name('short_received');
            Route::post('', 'Admins\AdminCargoController@receive_store')->name('store');

            Route::prefix('quick')->name('quick.')->group(function () {
                Route::get('', 'Admins\AdminCargoController@quick_receive_index')->name('index');
                Route::post('shipment_details', 'Admins\AdminCargoController@quick_receive_shipment_details')->name('shipment_details');
                Route::post('', 'Admins\AdminCargoController@quick_receive_store')->name('store');
                Route::get('list', 'Admins\AdminCargoController@quick_receive_list_index')->name('list.index');
                Route::get('list/ajax', 'Admins\AdminCargoController@quick_receive_list_ajax')->name('list.ajax');
                Route::post('list/ajax', 'Admins\AdminCargoController@quick_receive_list_details')->name('list.details');
            });
        });
        Route::post('piece_details', 'Admins\AdminCargoController@cargo_piece_details')->name('piece_details');
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

        Route::prefix('supply_chain')->name('supply_chain.')->group(function () {
            Route::get('','Admins\OrderManagementController@supply_chain_index')->name('supply_chain_index');
            Route::get('list','Admins\OrderManagementController@supply_chain_list')->name('supply_chain_list');

            Route::prefix('shipment_on_hold')->name('shipment_on_hold.')->group(function () {
                Route::get('','Admins\AdminSupplyChainController@shipment_on_hold_index')->name('index');
                Route::post('shipment_details', 'Admins\AdminSupplyChainController@shipment_on_hold_details')->name('shipment_details');
                Route::post('store', 'Admins\AdminSupplyChainController@shipment_on_hold_store')->name('store');

                //history
                Route::prefix('history')->name('history.')->group(function () {
                    Route::get('', 'Admins\AdminSupplyChainController@shipment_on_hold_history')->name('index');
                    Route::get('list', 'Admins\AdminSupplyChainController@shipment_on_hold_history_list')->name('list');
                    Route::post('allow_dispatch_delivery', 'Admins\AdminSupplyChainController@allow_dispatch_delivery')->name('allow_dispatch_delivery');
                });
            });
        });

        Route::prefix('mapping')->name('mapping.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@mapping_index')->name('index');
            Route::get('list', 'Admins\AdminCargoController@mapping_list')->name('list');
            Route::post('store', 'Admins\AdminCargoController@mapping_store')->name('store');
            Route::post('edit', 'Admins\AdminCargoController@mapping_edit')->name('edit');
            Route::post('update', 'Admins\AdminCargoController@mapping_edit_update')->name('update');
//            Route::post('print', 'Admins\AdminCargoController@history_cargo_print')->name('print');
            Route::prefix('manifest')->name('manifest.')->group(function () {
                Route::get('', 'Admins\AdminCargoManifestController@manifest_mapping_index')->name('index');
                Route::get('list', 'Admins\AdminCargoManifestController@manifest_mapping_list')->name('list');
                Route::post('store', 'Admins\AdminCargoManifestController@manifest_mapping_store')->name('store');
                Route::get('edit/{id}', 'Admins\AdminCargoManifestController@manifest_mapping_edit')->name('edit');
                Route::post('update/{id}', 'Admins\AdminCargoManifestController@manifest_mapping_edit_update')->name('update');
                Route::post('status', 'Admins\AdminCargoManifestController@manifest_mapping_status')->name('status');
            });
        });

    });
    Route::prefix('master_cargo')->name('master_cargo.')->group(function () {
        Route::prefix('bag')->name('bag.')->group(function () {
            Route::prefix('pending')->name('pending.')->group(function () {
                Route::get('', 'Admins\AdminMasterCargoController@pending_index')->name('index');
                Route::get('list', 'Admins\AdminMasterCargoController@pending_list')->name('list');
            });

            Route::prefix('create')->name('create.')->group(function () {
                Route::get('', 'Admins\AdminMasterCargoController@create_index')->name('index');
                Route::post('shipment_details', 'Admins\AdminMasterCargoController@create_shipment_details')->name('shipment_details');
                Route::post('bag_details', 'Admins\AdminMasterCargoController@create_bag_details')->name('bag_details');
                Route::get('seal_number', 'Admins\AdminMasterCargoController@create_bag_seal_number')->name('seal_number');
                Route::post('', 'Admins\AdminMasterCargoController@create_store')->name('store');

                Route::prefix('open_bag')->name('open_bag.')->group(function () {
                    Route::get('', 'Admins\AdminMasterCargoController@create_open_bag_index')->name('index');
                    Route::post('shipment_details', 'Admins\AdminMasterCargoController@create_open_bag_shipment_details')->name('shipment_details');
                    Route::post('', 'Admins\AdminMasterCargoController@create_open_bag_store')->name('store');
                });
            });
            Route::post('update_seal_number', 'Admins\AdminMasterCargoController@update_seal_number')->name('update_seal_number');
            Route::post('piece_details', 'Admins\AdminMasterCargoController@bag_piece_details')->name('piece_details');

            Route::prefix('history')->name('history.')->group(function () {
                Route::get('', 'Admins\AdminMasterCargoController@history_index')->name('index');
                Route::get('list', 'Admins\AdminMasterCargoController@history_list')->name('list');
            });

            Route::prefix('in_transit')->name('in_transit.')->group(function () {
                Route::get('', 'Admins\AdminMasterCargoController@master_cargo_in_transit_bag_index')->name('index');
                Route::get('list', 'Admins\AdminMasterCargoController@master_cargo_in_transit_bag_list')->name('list');
                Route::post('short_received', 'Admins\AdminMasterCargoController@master_cargo_in_transit_bag_short_received')->name('short_received');
                Route::post('receive', 'Admins\AdminMasterCargoController@master_cargo_in_transit_bag_receive')->name('receive');
            });

            Route::prefix('receive')->name('receive.')->group(function () {
                Route::get('', 'Admins\AdminMasterCargoController@master_cargo_bag_receive_index')->name('index');
                Route::post('shipment_details', 'Admins\AdminMasterCargoController@master_cargo_bag_receive_shipment_details')->name('shipment_details');
                Route::post('short_received', 'Admins\AdminMasterCargoController@master_cargo_bag_receive_short_received')->name('short_received');
                Route::post('', 'Admins\AdminMasterCargoController@master_cargo_bag_receive_store')->name('store');

                Route::prefix('quick')->name('quick.')->group(function () {
                    Route::get('', 'Admins\AdminMasterCargoController@master_cargo_bag_quick_receive_index')->name('index');
                    Route::post('bag_details', 'Admins\AdminMasterCargoController@master_cargo_bag_quick_receive_bag_details')->name('bag_details');
                    Route::post('', 'Admins\AdminMasterCargoController@master_cargo_bag_quick_receive_store')->name('store');
                });
            });
        });
        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('', 'Admins\AdminMasterCargoController@master_cargo_pending_index')->name('index');
            Route::get('list', 'Admins\AdminMasterCargoController@master_cargo_pending_list')->name('list');
            Route::post('shipments', 'Admins\AdminMasterCargoController@master_cargo_pending_shipments')->name('shipments');
        });

        Route::prefix('create')->name('create.')->group(function () {
            Route::get('{id?}', 'Admins\AdminMasterCargoController@master_cargo_create_index')->name('index');
            Route::post('bag_details', 'Admins\AdminMasterCargoController@create_master_cargo_bag_details')->name('bag_details');
            Route::post('bag_cargo_details', 'Admins\AdminMasterCargoController@create_master_cargo_details')->name('cargo_details');
            Route::post('', 'Admins\AdminMasterCargoController@master_cargo_create_store')->name('store');
        });
        Route::post('all_junctions', 'Admins\AdminMasterCargoController@master_cargo_in_transit_all_junctions')->name('all_junctions');

        Route::prefix('in_transit')->name('in_transit.')->group(function () {
            Route::get('', 'Admins\AdminMasterCargoController@master_cargo_in_transit_index')->name('index');
            Route::get('list', 'Admins\AdminMasterCargoController@master_cargo_in_transit_list')->name('list');
            Route::post('lost', 'Admins\AdminMasterCargoController@master_cargo_in_transit_lost')->name('lost');
            Route::post('bags', 'Admins\AdminMasterCargoController@master_cargo_in_transit_bags')->name('bags');
            Route::post('short_received_bags', 'Admins\AdminMasterCargoController@master_cargo_in_transit_short_received_bags')->name('short_received_bags');
            Route::post('shipments', 'Admins\AdminMasterCargoController@master_cargo_in_transit_shipments')->name('shipments');


            Route::post('print', 'Admins\AdminMasterCargoController@master_cargo_in_transit_print')->name('print');
            Route::post('junctions', 'Admins\AdminMasterCargoController@master_cargo_in_transit_junctions')->name('junctions');
            Route::post('details', 'Admins\AdminMasterCargoController@master_cargo_in_transit_details')->name('details');
            Route::post('receive_at_link', 'Admins\AdminMasterCargoController@master_cargo_in_transit_receive_at_link')->name('receive_at_link');
            Route::post('receive_at_link/store', 'Admins\AdminMasterCargoController@master_cargo_in_transit_receive_at_link_store')->name('receive_at_link.store');
            Route::post('receive', 'Admins\AdminMasterCargoController@master_cargo_in_transit_receive')->name('receive');
        });

        Route::prefix('receive')->name('receive.')->group(function () {
            Route::get('', 'Admins\AdminMasterCargoController@master_cargo_receive_index')->name('index');
            Route::post('bag_details', 'Admins\AdminMasterCargoController@master_cargo_receive_bag_details')->name('bag_details');
            Route::post('short_received', 'Admins\AdminMasterCargoController@master_cargo_receive_short_received')->name('short_received');
            Route::post('', 'Admins\AdminMasterCargoController@master_cargo_receive_store')->name('store');

            Route::prefix('quick')->name('quick.')->group(function () {
                Route::get('', 'Admins\AdminMasterCargoController@master_cargo_quick_receive_index')->name('index');
                Route::post('bag_details', 'Admins\AdminMasterCargoController@master_cargo_quick_receive_bag_details')->name('bag_details');
                Route::post('', 'Admins\AdminMasterCargoController@master_cargo_quick_receive_store')->name('store');
                Route::get('list', 'Admins\AdminMasterCargoController@master_cargo_quick_receive_list_index')->name('list.index');
                Route::get('list/ajax', 'Admins\AdminMasterCargoController@master_cargo_quick_receive_list_ajax')->name('list.ajax');
                Route::post('list/ajax', 'Admins\AdminMasterCargoController@master_cargo_quick_receive_list_details')->name('list.details');
            });
        });

        Route::prefix('history')->name('history.')->group(function () {
            Route::get('', 'Admins\AdminMasterCargoController@master_cargo_history_index')->name('index');
            Route::get('list', 'Admins\AdminMasterCargoController@master_cargo_history_list')->name('list');
        });

        Route::prefix('received')->name('received.')->group(function () {
            Route::get('', 'Admins\AdminMasterCargoController@master_cargo_received_index')->name('index');
            Route::get('list', 'Admins\AdminMasterCargoController@master_cargo_received_list')->name('list');
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
    Route::prefix('cargo_manifest')->name('cargo_manifest.')->group(function () {
        Route::post('print', 'Admins\AdminCargoManifestController@cargo_manifest_in_transit_print')->name('print');
        Route::prefix('bags')->name('bags.')->group(function () {
            Route::prefix('pending')->name('pending.')->group(function () {
                Route::get('', 'Admins\AdminCargoManifestController@pending_bag_index')->name('index');
                Route::get('list', 'Admins\AdminCargoManifestController@pending_bag_list')->name('list');
            });

            Route::prefix('create')->name('create.')->group(function () {
                Route::get('', 'Admins\AdminCargoManifestController@create_index')->name('index');
                Route::post('shipment_details', 'Admins\AdminCargoManifestController@create_shipment_details')->name('shipment_details');
                Route::post('bag_details', 'Admins\AdminCargoManifestController@create_bag_details')->name('bag_details');
                Route::get('seal_number', 'Admins\AdminCargoManifestController@create_bag_seal_number')->name('seal_number');
                Route::post('', 'Admins\AdminCargoManifestController@create_store')->name('store');

               Route::prefix('open_bag')->name('open_bag.')->group(function () {
                    Route::get('', 'Admins\AdminCargoManifestController@create_open_bag_index')->name('index');
//                    Route::post('shipment_details', 'Admins\AdminCargoManifestController@create_open_bag_shipment_details')->name('shipment_details');
                    Route::post('', 'Admins\AdminCargoManifestController@create_open_bag_store')->name('store');
                });

            });
            Route::post('piece_details', 'Admins\AdminCargoManifestController@bag_piece_details')->name('piece_details');

            Route::prefix('history')->name('history.')->group(function () {
                Route::get('', 'Admins\AdminCargoManifestController@history_index')->name('index');
                Route::get('list', 'Admins\AdminCargoManifestController@history_list')->name('list');
                Route::post('lost_shipments', 'Admins\AdminCargoManifestController@history_lost_shipments')->name('lost_shipments');
            });

        });
        Route::get('/','Admins\AdminCargoManifestController@manifest_index')->name('index');
        Route::get('/list','Admins\AdminCargoManifestController@manifest_list')->name('list');
        Route::get('/create','Admins\AdminCargoManifestController@create_manifest')->name('create');
        Route::post('bag/details','Admins\AdminCargoManifestController@bag_details')->name('bag_details');
        Route::post('cargo/details','Admins\AdminCargoManifestController@cargo_details')->name('cargo_details');
        Route::post('/store','Admins\AdminCargoManifestController@store_manifest')->name('store');
        Route::post('/update/seal_number','Admins\AdminCargoManifestController@update_seal_number')->name('update.seal_number');
        Route::post('/junctions','Admins\AdminCargoManifestController@junctions_info')->name('junctions_info');
        Route::post('/vehicle','Admins\AdminCargoManifestController@vehicle_info')->name('vehicle_info');
        Route::post('/transitted_shipments','Admins\AdminCargoManifestController@transitted_shipments')->name('transitted_shipments');
        Route::post('short_received_shipments', 'Admins\AdminCargoManifestController@short_received_shipments')->name('short_received_shipments');

        Route::prefix('receive')->name('receive.')->group(function () {
            Route::get('','Admins\AdminCargoManifestController@receive_bag_index')->name('index');
            Route::post('bag_details','Admins\AdminCargoManifestController@receive_bag_details')->name('bag_details');
            Route::post('store','Admins\AdminCargoManifestController@receive_bag_store')->name('store');

            Route::prefix('bag')->name('bag.')->group(function () {
                Route::get('','Admins\AdminCargoManifestController@receive_bag_shipments_index')->name('index');
                Route::post('details','Admins\AdminCargoManifestController@receive_bag_shipments_details')->name('details');
                Route::post('store','Admins\AdminCargoManifestController@receive_bag_shipments_store')->name('store');
            });
        });

        Route::get('history','Admins\AdminCargoManifestController@manifest_history')->name('history');
        Route::get('history/list','Admins\AdminCargoManifestController@manifest_history_list')->name('history.list');
        Route::post('history/bags','Admins\AdminCargoManifestController@manifest_bags')->name('history.bags');
        Route::post('history/short_received_bags','Admins\AdminCargoManifestController@cargo_short_received_bags')->name('history.short_received_bags');
        Route::post('history/shipments','Admins\AdminCargoManifestController@cargo_bag_shipments')->name('history.shipments');

        Route::prefix('draft')->name('draft.')->group(function () {
            Route::get('/list', 'Admins\AdminCargoManifestController@manifest_draft')->name('list');
            Route::post('/delete', 'Admins\AdminCargoManifestController@manifest_draft_delete')->name('delete');
            Route::get('setting/list', 'Admins\AdminCargoManifestController@manifest_draft_setting_list')->name('setting.list');
            Route::get('setting', 'Admins\AdminCargoManifestController@manifest_draft_setting')->name('setting');
            Route::post('update', 'Admins\AdminCargoManifestController@manifest_draft_update')->name('update');
        });

    });
    Route::prefix('dispute')->name('dispute.')->group(function (){
        Route::get('','Admins\DisputeController@dispute_index')->name('index');
        Route::get('list','Admins\DisputeController@dispute_list')->name('list');
        Route::post('create','Admins\DisputeController@dispute_create')->name('create');
        Route::post('get/shipments','Admins\DisputeController@get_shipments')->name('get.shipments');
        Route::post('resolve','Admins\DisputeController@resolve_dispute')->name('resolve');
        Route::post('bulk_resolve','Admins\DisputeController@bulk_resolve_dispute')->name('bulk_resolve');
        Route::post('update','Admins\DisputeController@update_dispute_view')->name('update');
        Route::put('update/submit','Admins\DisputeController@update_dispute')->name('update.submit');
        Route::post('data','Admins\DisputeController@get_data')->name('data');
        Route::post('create/universal','Admins\DisputeController@dispute_create_universal')->name('create.universal');

        Route::prefix('shipments')->name('shipments.')->group(function (){
            Route::get('','Admins\V2AdminDisputeShipmentsController@index')->name('index');
            Route::get('list','Admins\V2AdminDisputeShipmentsController@list')->name('list');
            Route::post('submit','Admins\V2AdminDisputeShipmentsController@add_submit')->name('submit');
            Route::post('update','Admins\V2AdminDisputeShipmentsController@dispute_update')->name('update');
            Route::post('images','Admins\V2AdminDisputeShipmentsController@dispute_images')->name('images');
        });

    });

    Route::prefix('tracking')->name('tracking.')->group(function() {
        Route::get('{tracking_number?}', 'Admins\AdminTrackingController@index')->name('index');
        Route::post('track', 'Admins\AdminTrackingController@track')->name('track');
        Route::post('track_v2', 'Admins\AdminTrackingController@track_v2')->name('track_v2');
        Route::post('rider_information', 'Admins\AdminTrackingController@rider_information')->name('rider_information');
        Route::post('rider_unresponsive_status', 'Admins\AdminTrackingController@rider_unresponsive_status')->name('rider_unresponsive_status');
        Route::post('cargo_consignment_details', 'Admins\AdminTrackingController@cargo_consignment_details')->name('cargo_consignment_details');
        Route::post('pieces_print', 'Admins\AdminTrackingController@pieces_print')->name('pieces_print');
        Route::post('estimation_check', 'Admins\AdminTrackingController@estimation_check')->name('estimation_check');

    });

    Route::prefix('quick_tracking')->name('quick_tracking.')->group(function() {
        Route::get('', 'Admins\AdminTrackingController@quick_tracking_index')->name('index');
        Route::post('info', 'Admins\AdminTrackingController@quick_tracking_shipment_info')->name('info');
    });
    Route::prefix('cx_quick_tracking')->name('cx_quick_tracking.')->group(function() {
        Route::get('', 'Admins\AdminTrackingController@cx_quick_tracking_index')->name('cx_index');
        Route::get('list', 'Admins\AdminTrackingController@cx_quick_tracking_list')->name('cx_list');
        Route::post('update', 'Admins\AdminTrackingController@cx_quick_tracking_update_consignee_info_and_special_instructions')->name('update');
    });

    Route::prefix('user_management')->name('user_management.')->group(function() {
        Route::prefix('users')->name('users.')->group(function() {
            Route::get('', 'Admins\UserManagementController@user_index')->name('index');
            Route::post('rejoin', 'Admins\UserManagementController@rejoin')->name('rejoin');
            Route::get('list', 'Admins\UserManagementController@user_list')->name('list');
            Route::get('email', 'Admins\UserManagementController@user_email')->name('email');
            Route::get('trax_id', 'Admins\UserManagementController@user_trax_id')->name('trax_id');
            Route::post('status', 'Admins\UserManagementController@user_status')->name('status');
            Route::post('assign_hubs', 'Admins\UserManagementController@user_assign_hub')->name('assign_hubs');

            Route::get('validate_phone','Admins\UserManagementController@validate_phone')->name('validate_phone');
            Route::prefix('add')->name('add.')->group(function() {
                Route::get('', 'Admins\UserManagementController@user_add_index')->name('index');
                Route::post('', 'Admins\UserManagementController@user_add_store')->name('store');
            });

            Route::prefix('update/{id}')->name('update.')->group(function() {
                Route::get('', 'Admins\UserManagementController@user_update_index')->name('index');
                Route::post('', 'Admins\UserManagementController@user_update_store')->name('store');
            });

            Route::post('user_info', 'Admins\UserManagementController@user_info')->name('user_info');
            Route::post('phone_update', 'Admins\UserManagementController@user_phone_update')->name('phone_update');

        });

        Route::prefix('fuel_management')->name('fuel_management.')->group(function (){
            Route::get('', 'Admins\Fuel\FuelManagementController@fuel_index')->name('index');
            Route::get('list', 'Admins\Fuel\FuelManagementController@fuel_list')->name('list');
            Route::get('request', 'Admins\Fuel\FuelManagementController@request_create')->name('create');
            Route::post('request', 'Admins\Fuel\FuelManagementController@request_store')->name('store');
            Route::post('request/approve', 'Admins\Fuel\FuelManagementController@request_approve')->name('approve');
            Route::post('request/edit', 'Admins\Fuel\FuelManagementController@request_edit')->name('edit');
            Route::get('request/search/card', 'Admins\Fuel\FuelManagementController@request_search_by_card')->name('search_by_card');
            Route::prefix('history')->name('history.')->group(function () {
                Route::get('', 'Admins\Fuel\FuelManagementController@request_history')->name('index');
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

        Route::prefix('user_requests')->name('user_requests.')->group(function() {
            Route::get('', 'Admins\AdminUserRequestController@user_requests_index')->name('index');
            Route::get('list', 'Admins\AdminUserRequestController@user_requests_list')->name('list');
            Route::get('email', 'Admins\AdminUserRequestController@user_email')->name('email');
            Route::get('trax_id', 'Admins\AdminUserRequestController@user_trax_id')->name('trax_id');
            Route::post('assign_hubs', 'Admins\AdminUserRequestController@user_assign_hub')->name('assign_hubs');
            Route::get('cnic', 'Admins\AdminUserRequestController@user_cnic')->name('cnic');

            Route::prefix('add')->name('add.')->group(function() {
                Route::get('', 'Admins\AdminUserRequestController@user_request_add_index')->name('index');
                Route::post('', 'Admins\AdminUserRequestController@user_add_store')->name('store');
            });
            Route::prefix('verify/{id}')->name('verify.')->group(function() {
                Route::get('', 'Admins\AdminUserRequestController@verify_index')->name('index');
                Route::post('', 'Admins\AdminUserRequestController@verify_store')->name('store');
            });
            Route::prefix('save/{id}')->name('save.')->group(function() {
                Route::get('', 'Admins\AdminUserRequestController@user_save_index')->name('index');
                Route::post('', 'Admins\AdminUserRequestController@user_save')->name('store');
            });
            Route::post('forward', 'Admins\AdminUserRequestController@forward')->name('forward');
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
            Route::post('reconcile_delivery_notes_excel', 'Admins\AdminFinanceController@outstanding_sdn_reconcile_delivery_notes_excel')->name('reconcile_delivery_notes_excel');
            Route::get('export_to_excel', 'Admins\AdminFinanceController@outstanding_sdn_export_to_excel')->name('export_to_excel');
            Route::post('deposit_slip_list', 'Admins\AdminFinanceController@outstanding_sdn_edit_deposit_slip')->name('deposit_slip_list');
            Route::post('edit', 'Admins\AdminFinanceController@outstanding_sdn_edit_deposit_slip_submit')->name('edit');
        });

        Route::prefix('outstanding_shipments')->name('outstanding_shipments.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@outstanding_shipments_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@outstanding_shipments_list')->name('list');
            Route::put('resolved', 'Admins\AdminFinanceController@outstanding_shipments_resolved')->name('resolved');
            Route::post('bulk_resolved', 'Admins\AdminFinanceController@outstanding_shipments_bulk_resolved')->name('bulk_resolved');
            Route::put('adjust_in_payment', 'Admins\AdminFinanceController@outstanding_shipments_adjust_in_payment')->name('adjust_in_payment');
            Route::post('bulk_adjust_in_payment', 'Admins\AdminFinanceController@outstanding_shipments_bulk_adjust_in_payment')->name('bulk_adjust_in_payment');
            Route::post('dncc/print', 'Admins\AdminFinanceController@outstanding_shipments_dncc_print')->name('dncc.print');
            Route::post('sdn/print', 'Admins\AdminFinanceController@outstanding_shipments_sdn_print')->name('sdn.print');
            Route::get('walk_in_index', 'Admins\AdminFinanceController@outstanding_walk_in_shipments_index')->name('walk_in_index');
            Route::get('walk_in_list', 'Admins\AdminFinanceController@outstanding_walk_in_shipments_list')->name('walk_in_list');
            Route::put('walk_in_resolved', 'Admins\AdminFinanceController@outstanding_walk_in_shipments_resolved')->name('walk_in_resolved');
            Route::post('walk_in_bulk_resolved', 'Admins\AdminFinanceController@outstanding_walk_in_shipments_bulk_resolved')->name('walk_in_bulk_resolved');
            Route::put('revert_request_shipments_check', 'Admins\AdminFinanceController@revert_request_shipments_check')->name('revert_request_shipments_check');
            Route::post('revert_request_submit', 'Admins\AdminFinanceController@revert_request_submit')->name('revert_request_submit');
            Route::get('revert_requested/{image_id}', 'Admins\AdminFinanceController@revert_requested_image')->name('revert_requested_image');
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
            Route::post('excel_store', 'Admins\AdminFinanceController@change_shipment_weight_excel_store')->name('excel_store');
            Route::post('calculate_amount', 'Admins\AdminFinanceController@change_shipment_weight_calculate_amount')->name('calculate_amount');

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
            Route::get('stats_calculate', 'Admins\AdminFinanceController@make_payments_stats_calculate')->name('stats_calculate');
        });

        Route::prefix('make_payments_pickup_wise')->name('make_payments_pickup_wise.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@make_payments_pickup_wise_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@make_payments_pickup_wise_list')->name('list');
            Route::post('delivered_shipments', 'Admins\AdminFinanceController@make_payments_delivered_shipments')->name('delivered_shipments');
            Route::post('returned_shipments', 'Admins\AdminFinanceController@make_payments_returned_shipments')->name('returned_shipments');
            Route::post('adjusted_shipments', 'Admins\AdminFinanceController@make_payments_adjusted_shipments')->name('adjusted_shipments');
            Route::post('shipment_details', 'Admins\AdminFinanceController@make_payments_shipment_details')->name('shipment_details');
            Route::get('shipment_list', 'Admins\AdminFinanceController@make_payments_shipment_list')->name('shipment_list');
            Route::get('shipment_export_selected', 'Admins\AdminFinanceController@make_payments_shipment_export_selected')->name('shipment_export_selected');
            Route::post('verify', 'Admins\AdminFinanceController@make_payments_verify')->name('verify');
            Route::get('export_bank_order', 'Admins\AdminFinanceController@make_payments_export_bank_order')->name('export_bank_order');
            Route::post('store', 'Admins\AdminFinanceController@make_payments_store')->name('store');
            Route::get('stats_calculate', 'Admins\AdminFinanceController@make_payments_stats_calculate')->name('stats_calculate');
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
            Route::get('generate_report_to_email', 'Admins\AdminFinanceController@done_payments_generate_report_to_email')->name('generate_report_to_email');
            Route::post('excel_store', 'Admins\AdminFinanceController@done_payments_excel_store')->name('excel_store');
            Route::get('view_status_history', 'Admins\AdminFinanceController@view_status_history')->name('view_status_history');
        });

        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@invoices_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@invoices_list')->name('list');
            Route::post('slip', 'Admins\AdminFinanceController@invoices_slip')->name('slip');
            Route::post('slip/view', 'Admins\AdminFinanceController@invoices_slip_view')->name('slip_view');
            Route::post('invoices_detail_print', 'Admins\AdminFinanceController@invoices_detail_print')->name('invoices_detail_print');
            Route::post('print', 'Admins\AdminFinanceController@corporate_invoice_print')->name('invoices_print');
            Route::post('print_origin_wise', 'Admins\AdminFinanceController@invoices_print_origin_wise')->name('print_origin_wise');
            Route::post('print_gst_wise', 'Admins\AdminFinanceController@invoices_print_gst_wise')->name('print_gst_wise');
            Route::get('export_to_excel', 'Admins\AdminFinanceController@invoices_export_to_excel')->name('export_to_excel');
            Route::put('email_reminder', 'Admins\AdminFinanceController@invoices_email_reminder')->name('email_reminder');
            Route::post('mark_as_received', 'Admins\AdminFinanceController@invoices_mark_as_received')->name('mark_as_received');
            Route::post('mark_as_received_all', 'Admins\AdminFinanceController@invoices_mark_as_received_all')->name('mark_as_received_all');
            Route::get('received', 'Admins\AdminFinanceController@received_invoices_index')->name('received_index');
            Route::get('received_list', 'Admins\AdminFinanceController@received_invoices_list')->name('received_list');
            //Route::get('download/{id}', 'Admins\AdminFinanceController@email_print_invoice')->name('download');

            Route::prefix('reimbursement')->name('reimbursement.')->group(function () {
                Route::get('', 'Admins\AdminFinanceController@reimbursement_invoices_index')->name('index');
                Route::get('list', 'Admins\AdminFinanceController@reimbursement_invoices_list')->name('list');
                Route::post('print_origin_wise', 'Admins\AdminFinanceController@reimbursement_invoices_print_origin_wise')->name('print_origin_wise');
                Route::post('print_gst_wise', 'Admins\AdminFinanceController@reimbursement_invoices_print_gst_wise')->name('print_gst_wise');
                Route::get('export_to_excel', 'Admins\AdminFinanceController@reimbursement_invoices_export_to_excel')->name('export_to_excel');
                Route::post('detail_print', 'Admins\AdminFinanceController@reimbursement_detail_invoices_print')->name('detail_print');
                Route::post('print', 'Admins\AdminFinanceController@reimbursement_invoice_print')->name('invoices_print');
            });
        });

        Route::prefix('invoice_for_reimbursement')->name('invoice_for_reimbursement.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@invoice_for_reimbursement_index')->name('index');
            Route::get('generate', 'Admins\AdminFinanceController@invoice_for_reimbursement_generate')->name('generate');
        });
        Route::prefix('ftl_invoice')->name('ftl_invoice.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@ftl_invoice_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@ftl_invoice_list')->name('list');
            Route::post('received', 'Admins\AdminFinanceController@ftl_invoice_received')->name('received');
            Route::post('print', 'Admins\AdminFinanceController@ftl_invoice_print')->name('print');
            Route::get('export_to_excel', 'Admins\AdminFinanceController@ftl_invoice_export_to_excel')->name('export_to_excel');


        });

        Route::prefix('retail')->name('retail.')->group(function () {

            Route::prefix('make_payments')->name('make_payments.')->group(function () {
                Route::get('', 'Admins\AdminFinanceController@retail_make_payments_index')->name('index');
                Route::get('list', 'Admins\AdminFinanceController@retail_make_payments_list')->name('list');
                Route::post('delivered_shipments', 'Admins\AdminFinanceController@retail_make_payments_delivered_shipments')->name('delivered_shipments');
                Route::post('adjusted_shipments', 'Admins\AdminFinanceController@retail_make_payments_adjusted_shipments')->name('adjusted_shipments');
                Route::post('shipment_details', 'Admins\AdminFinanceController@retail_make_payments_shipment_details')->name('shipment_details');
                Route::get('shipment_list', 'Admins\AdminFinanceController@retail_make_payments_shipment_list')->name('shipment_list');
                Route::get('shipment_export_selected', 'Admins\AdminFinanceController@retail_make_payments_shipment_export_selected')->name('shipment_export_selected');
                Route::post('verify', 'Admins\AdminFinanceController@retail_make_payments_verify')->name('verify');
                Route::get('export_bank_order', 'Admins\AdminFinanceController@retail_make_payments_export_bank_order')->name('export_bank_order');
                Route::post('store', 'Admins\AdminFinanceController@retail_make_payments_store')->name('store');
                Route::get('stats_calculate', 'Admins\AdminFinanceController@retail_make_payments_stats_calculate')->name('stats_calculate');
            });

            Route::prefix('done_payments')->name('done_payments.')->group(function () {
                Route::get('', 'Admins\AdminFinanceController@retail_done_payments_index')->name('index');
                Route::get('list', 'Admins\AdminFinanceController@retail_done_payments_list')->name('list');
                Route::put('paid', 'Admins\AdminFinanceController@retail_done_payments_paid')->name('paid');
                Route::put('reverted', 'Admins\AdminFinanceController@retail_done_payments_reverted')->name('reverted');
                Route::post('delivered_shipments', 'Admins\AdminFinanceController@retail_done_payments_delivered_shipments')->name('delivered_shipments');
                Route::post('adjusted_shipments', 'Admins\AdminFinanceController@retail_done_payments_adjusted_shipments')->name('adjusted_shipments');
                Route::post('details_print', 'Admins\AdminFinanceController@retail_done_payments_details_print')->name('details_print');
                Route::post('details', 'Admins\AdminFinanceController@retail_done_payments_details')->name('details');
                Route::put('update_details', 'Admins\AdminFinanceController@retail_done_payments_update_details')->name('update_details');
                Route::get('export_to_excel', 'Admins\AdminFinanceController@retail_done_payments_export_to_excel')->name('export_to_excel');
                Route::post('excel_store', 'Admins\AdminFinanceController@retail_done_payments_excel_store')->name('excel_store');
                Route::get('retail_generate_report_to_email', 'Admins\AdminFinanceController@retail_done_payments_generate_report_to_email')->name('retail_generate_report_to_email');
            });

        });
    });

    Route::prefix('petty_cash')->name('petty_cash.')->group(function() {
        Route::prefix('make')->name('make.')->group(function (){
            Route::get('', 'Admins\AdminPettyCashController@make_petty_cash_statement_index')->name('index');
            Route::post('destination', 'Admins\AdminPettyCashController@make_petty_cash_statement_check_destination')->name('destination');
            Route::post('hubs', 'Admins\AdminPettyCashController@make_petty_cash_statement_get_hubs')->name('hubs');
            Route::post('cities', 'Admins\AdminPettyCashController@make_petty_cash_statement_get_cities')->name('cities');
            Route::post('dncc', 'Admins\AdminPettyCashController@make_petty_cash_statement_get_dncc')->name('dncc');
            Route::post('employee', 'Admins\AdminPettyCashController@make_petty_cash_statement_get_employee')->name('employee');
            Route::post('reference', 'Admins\AdminPettyCashController@make_petty_cash_statement_check_reference')->name('reference');
            Route::post('titles', 'Admins\AdminPettyCashController@make_petty_cash_statement_titles')->name('titles');
            Route::post('submit', 'Admins\AdminPettyCashController@make_petty_cash_statement_submit')->name('submit');
        });
        Route::post('view/sdn_logs','Admins\AdminPettyCashController@sdn_log')->name('sdn_logs');
        Route::prefix('statements')->name('statements.')->group(function (){
            Route::get('', 'Admins\AdminPettyCashController@petty_cash_statements_index')->name('index');
            Route::get('list', 'Admins\AdminPettyCashController@petty_cash_statements_list')->name('list');
            Route::post('print', 'Admins\AdminPettyCashController@statement_print')->name('print');
            Route::post('approve', 'Admins\AdminPettyCashController@petty_cash_statements_approve')->name('approve');
            Route::post('reject_all', 'Admins\AdminPettyCashController@petty_cash_statements_reject_all')->name('reject_all');
            Route::get('{id}/edit', 'Admins\AdminPettyCashController@edit_petty_cash_statement_index')->name('edit');
            Route::get('{id}/edit/list', 'Admins\AdminPettyCashController@edit_petty_cash_statement_list')->name('edit.list');
            Route::post('edit/approve', 'Admins\AdminPettyCashController@edit_petty_cash_statements_approve')->name('edit.approve');
            Route::post('edit/reject', 'Admins\AdminPettyCashController@edit_petty_cash_statements_reject')->name('edit.reject');
            Route::put('edit/submit', 'Admins\AdminPettyCashController@edit_petty_cash_statements_submit')->name('edit.submit');
            Route::post('view/amount', 'Admins\AdminPettyCashController@edit_petty_cash_statements_amount')->name('view.amount');
            Route::get('reference_document/{reference_document}', 'Admins\AdminPettyCashController@reference_document')->name('reference_document');
            Route::post('station_operation_finance_approved','Admins\AdminPettyCashController@petty_cash_station_operation_finance_approved_all')->name('station_operation_finance_approved');
            Route::post('check','Admins\AdminPettyCashController@petty_cash_statement_check')->name('check');

        });
        Route::prefix('approved')->name('approved.')->group(function (){
            Route::get('', 'Admins\AdminPettyCashController@approved_petty_cash_statements_index')->name('index');
            Route::get('list', 'Admins\AdminPettyCashController@approved_petty_cash_statements_list')->name('list');
            Route::get('{id}/view', 'Admins\AdminPettyCashController@approved_petty_cash_statements_view')->name('view');
            Route::get('{id}/view/list', 'Admins\AdminPettyCashController@approved_petty_cash_statements_view_list')->name('view.list');
            Route::post('paid', 'Admins\AdminPettyCashController@approved_petty_cash_statements_paid')->name('paid');
            Route::post('adjusted', 'Admins\AdminPettyCashController@approved_petty_cash_statements_adjusted')->name('adjusted');
            Route::post('bulk_adjusted', 'Admins\AdminPettyCashController@approved_petty_cash_statements_bulk_adjusted')->name('bulk_adjusted');
        });
        Route::prefix('rejected')->name('rejected.')->group(function (){
            Route::get('', 'Admins\AdminPettyCashController@rejected_petty_cash_statements_index')->name('index');
            Route::get('list', 'Admins\AdminPettyCashController@rejected_petty_cash_statements_list')->name('list');
        });
        Route::prefix('draft')->name('draft.')->group(function (){
            Route::get('', 'Admins\AdminPettyCashController@draft_petty_cash_statements_index')->name('index');
            Route::get('list', 'Admins\AdminPettyCashController@draft_petty_cash_statements_list')->name('list');
            Route::get('{id}/edit', 'Admins\AdminPettyCashController@draft_edit_petty_cash_statement_index')->name('edit');
            Route::get('{id}/edit/list', 'Admins\AdminPettyCashController@draft_edit_petty_cash_statement_list')->name('edit.list');
            Route::put('edit/submit', 'Admins\AdminPettyCashController@draft_edit_petty_cash_statements_submit')->name('edit.submit');
            Route::get('reference_document/{reference_document}', 'Admins\AdminPettyCashController@draft_reference_document')->name('reference_document');

        });
    });

    Route::prefix('month_closing')->name('month_closing.')->group(function (){
        Route::get('','Admins\AdminMonthClosingController@month_closing_index')->name('index');
        Route::get('list','Admins\AdminMonthClosingController@month_closing_list')->name('list');
        Route::post('add','Admins\AdminMonthClosingController@add_shipment')->name('add');
        Route::post('confirm','Admins\AdminMonthClosingController@return_confirm_shipment')->name('confirm');
        Route::post('reattempt','Admins\AdminMonthClosingController@return_reattempt_shipment')->name('reattempt');


        Route::prefix('pending')->name('pending.')->group(function(){
            Route::get('','Admins\AdminMonthClosingController@pending_index')->name('index');
            Route::get('list','Admins\AdminMonthClosingController@pending_list')->name('list');
            Route::post('assign','Admins\AdminMonthClosingController@assign_responsible_submit')->name('assign');
            Route::post('closing_type_update','Admins\AdminMonthClosingController@closing_status_submit')->name('closing_type_update');
            Route::post('resolved','Admins\AdminMonthClosingController@month_closing_resolved')->name('resolved');

            Route::post('assign_details','Admins\AdminMonthClosingController@edit_assign_details')->name('assign_details');
            Route::post('assign_update','Admins\AdminMonthClosingController@assign_responsible_update')->name('assign_update');

        });

        Route::prefix('resolved')->name('resolved.')->group(function(){
            Route::get('','Admins\AdminMonthClosingController@resolved_index')->name('index');
            Route::get('list','Admins\AdminMonthClosingController@resolved_list')->name('list');
            Route::post('closed','Admins\AdminMonthClosingController@month_closing_closed')->name('closed');

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
            Route::put('','Admins\AdminPackagingMaterialController@request_update')->name('update');
            Route::post('submit','Admins\AdminPackagingMaterialController@request_submit')->name('submit');
            Route::post('check_quantity','Admins\AdminPackagingMaterialController@request_check_quantity')->name('check_quantity');
            Route::post('dispatch','Admins\AdminPackagingMaterialController@request_dispatch_submit')->name('dispatch');
            Route::post('quantity_details','Admins\AdminPackagingMaterialController@quantity_details')->name('quantity_details');
            Route::post('confirm','Admins\AdminPackagingMaterialController@request_confirm')->name('confirm');
            Route::post('cancel','Admins\AdminPackagingMaterialController@request_cancel')->name('cancel');
            Route::post('replenish','Admins\AdminPackagingMaterialController@request_replenish')->name('replenish');
            Route::post('completed','Admins\AdminPackagingMaterialController@request_completed')->name('completed');
            Route::post('good_receiving_note','Admins\AdminPackagingMaterialController@good_receiving_note')->name('good_receiving_note');
            Route::post('sizes','Admins\AdminPackagingMaterialController@packaging_request_sizes')->name('sizes');
            Route::post('remarks','Admins\AdminPackagingMaterialController@packaging_request_remarks')->name('remarks');
            Route::get('pickup_address','Admins\AdminPackagingMaterialController@fetch_pickup_address')->name('pickup_address');
            Route::post('submit','Admins\AdminPackagingMaterialController@packaging_request_submit')->name('submit');

        });
        Route::prefix('types')->name('types.')->group(function (){
            Route::get('','Admins\AdminPackagingMaterialController@types_index')->name('index');
            Route::get('list','Admins\AdminPackagingMaterialController@types_list')->name('list');
            Route::post('add','Admins\AdminPackagingMaterialController@type_add')->name('add');
            Route::get('all_shippers','Admins\AdminPackagingMaterialController@all_shippers')->name('all_shippers');
            Route::get('all_shippers_edit','Admins\AdminPackagingMaterialController@all_shippers_edit')->name('all_shippers_edit');

            Route::post('details','Admins\AdminPackagingMaterialController@type_details')->name('details');
            Route::post('edit','Admins\AdminPackagingMaterialController@type_edit')->name('edit');
            Route::post('enable_disable','Admins\AdminPackagingMaterialController@type_enable_disable')->name('enable_disable');
        });
        Route::prefix('warehouse')->name('warehouse.')->group(function (){
            Route::get('','Admins\AdminPackagingMaterialController@warehouse_index')->name('index');
            Route::get('list','Admins\AdminPackagingMaterialController@warehouse_list')->name('list');
            Route::post('enable_disable','Admins\AdminPackagingMaterialController@warehouse_enable_disable')->name('enable_disable');
            Route::post('add','Admins\AdminPackagingMaterialController@warehouse_add')->name('add');
            Route::post('edit_data','Admins\AdminPackagingMaterialController@warehouse_edit_data')->name('edit_data');
            Route::post('edit','Admins\AdminPackagingMaterialController@warehouse_edit')->name('edit');
            Route::post('master_add','Admins\AdminPackagingMaterialController@warehouse_master_add')->name('master_add');
            Route::post('warehouse_hubs','Admins\AdminPackagingMaterialController@warehouse_hubs')->name('warehouse_hubs');
        });
        Route::prefix('stock_request')->name('stock_request.')->group(function (){
            Route::post('cancel','Admins\AdminPackagingMaterialController@stock_request_cancel')->name('cancel');
            Route::post('confirm','Admins\AdminPackagingMaterialController@stock_request_confirm')->name('confirm');
            Route::post('details','Admins\AdminPackagingMaterialController@stock_request_details')->name('details');
            Route::post('dispatch','Admins\AdminPackagingMaterialController@stock_request_dispatch')->name('dispatch');

        });
        Route::prefix('stock_send')->name('stock_send.')->group(function (){
            Route::post('submit','Admins\AdminPackagingMaterialController@stock_send_submit')->name('submit');
        });

        Route::prefix('inventory')->name('inventory.')->group(function (){
            Route::get('','Admins\AdminPackagingMaterialController@inventory_index')->name('index');
            Route::post('list','Admins\AdminPackagingMaterialController@inventory_list')->name('list');
        });
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('', 'Admins\AdminNotificationsController@index')->name('index');
        Route::get('list', 'Admins\AdminNotificationsController@list')->name('list');
        Route::post('send_custom_email', 'Admins\AdminNotificationsController@send_custom_email')->name('send_custom_email');
        Route::post('details', 'Admins\AdminNotificationsController@details')->name('details');
        Route::post('status', 'Admins\AdminNotificationsController@status')->name('status');
        Route::post('edit', 'Admins\AdminNotificationsController@edit')->name('edit');
        Route::post('send_custom_notification', 'Admins\AdminNotificationsController@send_custom_notification')->name('send_custom_notification');
    });

    Route::prefix('app_notifications')->name('app_notifications.')->group(function () {
        Route::get('', 'Admins\AdminNotificationsController@app_notification_index')->name('index');
        Route::get('list', 'Admins\AdminNotificationsController@app_notification_list')->name('list');
        Route::post('status', 'Admins\AdminNotificationsController@app_notification_status')->name('status');
        Route::post('details', 'Admins\AdminNotificationsController@app_notification_details')->name('details');
        Route::post('edit', 'Admins\AdminNotificationsController@app_notification_edit')->name('edit');
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
            Route::post('delivered_shipments', 'Admins\AdminReportsController@history_delivered_shipments')->name('delivered_shipments');
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
        Route::prefix('multiple_iban')->name('multiple_iban.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@multiple_iban_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@multiple_iban_list')->name('list');
        });
        Route::prefix('completed_aging')->name('completed_aging.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@completed_aging_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@completed_aging_list')->name('list');
        });
        Route::prefix('pending_cash_collection')->name('pending_cash_collection.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@pending_cash_collection_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@pending_cash_collection_list')->name('list');
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
        Route::prefix('rider_unresponsive_report')->name('rider_unresponsive_report.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@rider_unresponsive_report_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@rider_unresponsive_report_list')->name('list');
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
            Route::get('agent-report', 'Admins\AdminReportsController@debriefing_agent_report')->name('agent_index');
            Route::get('agent-report/list', 'Admins\AdminReportsController@debriefing_agent_report_list')->name('agent_list');
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
        Route::prefix('account_activation')->name('account_activation.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@account_activation_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@account_activation_list')->name('list');
        });
        Route::prefix('adjustments')->name('adjustments.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@adjustments_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@adjustments_list')->name('list');

        });

        Route::prefix('sdn')->name('sdn.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@sdn_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@sdn_list')->name('list');

        });
        Route::prefix('account_edit')->name('account_edit.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@account_edit_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@account_edit_list')->name('list');
        });
        Route::prefix('bank_history')->name('bank_history.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@bank_history_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@bank_history_list')->name('list');
        });

        Route::prefix('consignee_details')->name('consignee_details.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@consignee_details_history_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@consignee_details_history_list')->name('list');
        });
        Route::prefix('booked_and_cancelled')->name('booked_and_cancelled.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@booked_and_cancelled_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@booked_and_cancelled_list')->name('list');
        });

        Route::prefix('fake_status_shipments')->name('fake_status_shipments.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@fake_status_shipments_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@fake_status_shipments_list')->name('list');
        });
        Route::prefix('daily_visit')->name('daily_visit.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@daily_visit_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@daily_visit_list')->name('list');
        });
        Route::prefix('delivered_shipment')->name('delivered_shipment.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@delivered_shipment_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@delivered_shipment_list')->name('list');
        });
        Route::prefix('route_distribution')->name('route_distribution.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@route_distribution_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@route_distribution_list')->name('list');
        });
        Route::prefix('destination_delivery_received')->name('destination_delivery_received.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@destination_delivery_received_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@destination_delivery_received_list')->name('list');
        });

        Route::prefix('account_reconciliation')->name('account_reconciliation.')->group(function (){
            Route::get('', 'Reports\AccountReconciliationController@account_reconciliation_index')->name('index');
            Route::post('export_to_excel', 'Reports\AccountReconciliationController@account_reconciliation_export_to_excel')->name('export_to_excel');
            Route::get('download', 'Reports\AccountReconciliationController@account_reconciliation_download')->name('download');
        });
        Route::prefix('cargo_short_received_shipments')->name('cargo_short_received_shipments.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@cargo_short_received_shipments_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@cargo_short_received_shipments_list')->name('list');
        });

        Route::prefix('last_mile_status')->name('last_mile_status.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@last_mile_status_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@last_mile_status_list')->name('list');
        });

        Route::prefix('pickup_report')->name('pickup_report.')->group(function (){
            Route::get('', 'Admins\V2Pickup\V2AdminReportController@pickup_report_index')->name('index');
            Route::get('list', 'Admins\V2Pickup\V2AdminReportController@pickup_report_list')->name('list');
            Route::post('/data', 'Admins\V2Pickup\V2AdminReportController@pickup_report_data')->name('data');
        });

        Route::prefix('not_attempted_aging')->name('not_attempted_aging.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@not_attempted_aging_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@not_attempted_aging_list')->name('list');
        });
		Route::prefix('station_recovery')->name('station_recovery.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@station_recovery_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@station_recovery_list')->name('list');
            Route::post('update', 'Admins\AdminReportsController@station_recovery_update')->name('update');
        });
        Route::prefix('daily_monthly_adjustment')->name('daily_monthly_adjustment.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@daily_monthly_adjustment_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@daily_monthly_adjustment_list')->name('list');
            Route::get('summary_list', 'Admins\AdminReportsController@daily_monthly_adjustment_summary_list')->name('summary_list');
        });
        Route::prefix('petty_cash_expense_summary')->name('petty_cash_expense_summary.')->group(function (){
            Route::get('', 'Admins\PettyCashExpenseSummaryReport@index')->name('index');
            Route::get('petty_cash_summary_report', 'Admins\PettyCashExpenseSummaryReport@pettyCashSummaryReportProcess')->name('petty_cash_summary_report');
        });
        Route::prefix('app_efficiency')->name('app_efficiency.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@app_efficiency_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@app_efficiency_list')->name('app_efficiency_list');
        });
        Route::prefix('sales_incentive')->name('sales_incentive.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@sales_incentive_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@sales_incentive_list')->name('list');
            Route::get('consolidated', 'Admins\AdminReportsController@consolidated_sales_incentive_index')->name('consolidated');
            Route::get('consolidated_list', 'Admins\AdminReportsController@consolidated_sales_incentive_list')->name('consolidated_list');
        });
        Route::prefix('month_closing')->name('month_closing.')->group(function(){
            Route::prefix('individual')->name('individual.')->group(function (){
                Route::get('', 'Admins\AdminMonthClosingReportsController@month_closing_individual_index')->name('index');
                Route::get('list', 'Admins\AdminMonthClosingReportsController@month_closing_individual_list')->name('list');
            });
            Route::prefix('pivot')->name('pivot.')->group(function (){
                Route::get('', 'Admins\AdminMonthClosingReportsController@month_closing_pivot_index')->name('index');
                Route::get('list', 'Admins\AdminMonthClosingReportsController@month_closing_pivot_list')->name('list');
            });


        });
        Route::prefix('osa_charges')->name('osa_charges.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@osa_charges_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@osa_charges_list')->name('list');
        });
//        Route::prefix('confirmation_pending_report')->name('confirmation_pending_report.')->group(function (){
//            Route::get('', 'Admins\AdminReportsController@confirmation_shipments_index')->name('index');
//            Route::get('list', 'Admins\AdminReportsController@confirmation_shipments_list')->name('list');
//        });

        Route::prefix('last_mile_app')->name('last_mile_app.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@last_mile_app_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@last_mile_app_list')->name('list');
            Route::get('app_shipments_list', 'Admins\AdminReportsController@last_mile_app_shipments_list')->name('app_shipments_list');
            Route::get('dbf_shipments_list', 'Admins\AdminReportsController@last_mile_dbf_shipments_list')->name('dbf_shipments_list');

        });
        Route::prefix('weight_qc')->name('weight_qc.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@weight_qc_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@weight_qc_list')->name('list');
        });
        Route::prefix('master_cargo')->name('master_cargo.')->group(function (){
            Route::prefix('bag')->name('bag.')->group(function (){
                Route::prefix('in_transit')->name('in_transit.')->group(function (){
                    Route::get('', 'Admins\AdminReportsController@in_transit_index')->name('index');
                    Route::get('list', 'Admins\AdminReportsController@in_transit_list')->name('list');
                    Route::post('shipments', 'Admins\AdminReportsController@in_transit_shipments')->name('shipments');
                    Route::post('short_received', 'Admins\AdminReportsController@short_received_shipments')->name('short_received');
                });
            });
            Route::prefix('short_received_shipments')->name('short_received_shipments.')->group(function (){
                Route::get('', 'Admins\AdminReportsController@master_cargo_short_received_shipments_index')->name('index');
                Route::get('list', 'Admins\AdminReportsController@master_cargo_short_received_shipments_list')->name('list');
            });

        });

        Route::prefix('retail_sales')->name('retail_sales.')->group(function (){
            Route::get('', 'Admins\AdminRetailReportController@sales_index')->name('index');
            Route::post('list', 'Admins\AdminRetailReportController@sales_list')->name('list');

        });

        Route::prefix('shipper_insurance')->name('shipper_insurance.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@shipper_insurance_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@shipper_insurance_list')->name('list');
            Route::post('charges', 'Admins\AdminReportsController@shipper_insurance_charges')->name('charges');

        });

        Route::prefix('operation_service_level')->name('operation_service_level.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@operation_service_level_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@operation_service_level_list')->name('list');
        });
        Route::prefix('work_code_master')->name('work_code_master.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@work_code_master_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@work_code_master_list')->name('list');
        });

        Route::prefix('manifest')->name('manifest.')->group(function () {
            Route::prefix('short_received_shipments')->name('short_received_shipments.')->group(function (){
                Route::get('', 'Admins\AdminReportsController@manifest_short_received_shipments_index')->name('index');
                Route::get('list', 'Admins\AdminReportsController@manifest_short_received_shipments_list')->name('list');
            });
        });

        Route::prefix('reverse_pickup')->name('reverse_pickup.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@reverse_pickup_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@reverse_pickup_list')->name('list');
        });
        Route::prefix('dws_report')->name('dws_report.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@dws_report_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@dws_report_list')->name('list');
        });
        Route::prefix('revert')->name('revert.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@return_revert_log')->name('index');
            Route::get('list', 'Admins\AdminReportsController@return_revert_list')->name('list');
        });

        Route::prefix('pickup_history_cn_wise')->name('pickup_history_cn_wise.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@pickup_history_cn_wise_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@pickup_history_cn_wise_list')->name('list');
        });
        Route::prefix('crm_count')->name('crm_count.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@crm_count_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@crm_count_list')->name('list');
        });
        Route::prefix('crm_special_approval')->name('crm_special_approval.')->group(function (){
            Route::get('', 'Admins\AdminReportsController@crm_special_approval_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@crm_special_approval_list')->name('list');
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
    Route::post('get/otp','Auth\AdminForgotPasswordController@GenerateOTP')->name('password.generate.otp');
    Route::get('password/reset','Auth\AdminForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/reset/pin','Auth\AdminResetPasswordController@reset_pin')->name('password.reset.pin');
    Route::post('password/reset','Auth\AdminResetPasswordController@reset')->name('password.reset');
    Route::get('password/reset/{token}','Auth\AdminResetPasswordController@showResetForm')->name('password.reset');

    Route::prefix('sales')->name('sales.')->group(function () {
        Route::prefix('territory')->name('territory.')->group(function () {
            Route::get('', 'Admins\SalesIncentiveController@territoryindex')->name('territoryindex');
            Route::get('list', 'Admins\SalesIncentiveController@territory_list')->name('list');
            Route::post('users', 'Admins\SalesIncentiveController@territory_users')->name('users');
            Route::post('add', 'Admins\SalesIncentiveController@territory_add')->name('add');
            Route::get('edit/{id}', 'Admins\SalesIncentiveController@territory_edit')->name('edit');
            Route::put('update/{id}', 'Admins\SalesIncentiveController@territory_update')->name('update');
            Route::post('city_admins', 'Admins\SalesIncentiveController@territory_city_admins')->name('city_admins');
            Route::post('status', 'Admins\SalesIncentiveController@territory_enable_disable')->name('status');
        });
        Route::prefix('designation')->name('designation.')->group(function () {
            Route::get('', 'Admins\SalesIncentiveController@designationindex')->name('designationindex');
            Route::get('list', 'Admins\SalesIncentiveController@designation_list')->name('list');
            Route::post('add', 'Admins\SalesIncentiveController@designation_add')->name('add');
            Route::get('edit/{id}', 'Admins\SalesIncentiveController@designation_edit')->name('edit');
            Route::put('update/{id}', 'Admins\SalesIncentiveController@designation_update')->name('update');
            Route::post('status', 'Admins\SalesIncentiveController@designation_enable_disable')->name('status');
        });
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::prefix('pickup')->name('pickup.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@pickup_index')->name('index');
            Route::post('weight/add', 'Admins\GlobalSettingsController@add_pickup_weight')->name('weight.add');
            Route::put('weight/add', 'Admins\GlobalSettingsController@add_pickup_weight')->name('weight.add');
            Route::get('pickup_settings', 'Admins\GlobalSettingsController@pickup_cut_off_settings_index')->name('pickup_settings');
            Route::post('pickup_settings_store', 'Admins\GlobalSettingsController@pickup_cut_off_settings_store')->name('pickup_settings_store');
        });

        Route::prefix('shippers')->name('shippers.')->group(function (){
            Route::prefix('status_webhook')->name('status_webhook.')->group(function (){
                Route::get('','Admins\GlobalSettingsController@status_webhook_index')->name('index');
                Route::get('list','Admins\GlobalSettingsController@status_webhook_list')->name('list');
                Route::get('{id}/edit','Admins\GlobalSettingsController@status_webhook_edit')->name('edit');
                Route::put('update','Admins\GlobalSettingsController@status_webhook_update')->name('update');
            });
        });

        Route::prefix('fleet')->name('fleet.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@fleet_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@fleet_list')->name('list');
            Route::post('store', 'Admins\GlobalSettingsController@fleet_store')->name('store');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@fleet_enable_disable')->name('enable_disable');
            Route::get('unique', 'Admins\GlobalSettingsController@fleet_unique')->name('unique');
            Route::get('{id}/edit/form', 'Admins\GlobalSettingsController@fleet_edit')->name('edit');
            Route::put('{id}/update', 'Admins\GlobalSettingsController@fleet_update')->name('update');
            Route::post('store/driver', 'Admins\GlobalSettingsController@fleet_store_driver')->name('store.driver');
            Route::post('store/vendor', 'Admins\GlobalSettingsController@fleet_store_vendor')->name('store.vendor');
            Route::get('unique/cnic', 'Admins\GlobalSettingsController@fleet_cnic_unique')->name('unique.cnic');
        });

        Route::prefix('route_management')->name('route_management.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@route_management_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@route_management_list')->name('list');
            Route::post('store', 'Admins\GlobalSettingsController@route_management_store')->name('store');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@route_management_enable_disable')->name('enable_disable');
            Route::get('unique', 'Admins\GlobalSettingsController@route_management_unique')->name('unique');
            Route::get('{id}/edit/form', 'Admins\GlobalSettingsController@route_management_edit')->name('edit');
            Route::put('{id}/update', 'Admins\GlobalSettingsController@route_management_update')->name('update');


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

        Route::prefix('rider_ticker')->name('rider_ticker.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@rider_ticker_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@rider_ticker_store')->name('store');
            Route::post('admin_store', 'Admins\GlobalSettingsController@admin_ticker_store')->name('admin_store');
            Route::post('retail_store', 'Admins\GlobalSettingsController@retail_ticker_store')->name('retail_store');
        });

        Route::prefix('walk_in')->name('walk_in.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@walk_in_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@walk_in_store')->name('store');
        });
        Route::prefix('international_walk_in')->name('international_walk_in.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@international_walk_in_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@international_walk_in_store')->name('store');
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
            Route::prefix('hub-assigning')->name('consignee.')->group( function(){
                Route::get('', 'Admins\GlobalSettingsController@petty_cash_consignee_index')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@petty_cash_consignee_list')->name('list');
                Route::Post('', 'Admins\GlobalSettingsController@petty_cash_consignee_store')->name('store');
                Route::Post('edit', 'Admins\GlobalSettingsController@petty_cash_consignee_edit')->name('edit');
                Route::prefix('city')->name('city.')->group( function(){
                    Route::get('{id}', 'Admins\GlobalSettingsController@petty_cash_consignee_city_index')->name('index');
                    Route::Post('{id}/check', 'Admins\GlobalSettingsController@petty_cash_consignee_city_check')->name('check');
                    Route::Post('{id}', 'Admins\GlobalSettingsController@petty_cash_consignee_city_update')->name('update');
                });
            });
        });

        Route::prefix('debriefing_report_cut_off_time')->name('debriefing_report_cut_off_time.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@debriefing_report_cut_off_time_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@debriefing_report_cut_off_time_store')->name('store');
        });

        Route::prefix('debriefing_break_time')->name('debriefing_break_time.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@debriefing_break_time_setting_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@debriefing_break_time_setting_store')->name('store');
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

        Route::prefix('ibft_charges')->name('ibft_charges.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@ibft_charges_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@ibft_charges_store')->name('store');
        });
        Route::prefix('weight_factor')->name('weight_factor.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@weight_factor_index')->name('index');
            Route::post('update', 'Admins\GlobalSettingsController@weight_factor_update')->name('update');
        });
        Route::prefix('delivery_call_verification_ratio')->name('delivery_call_verification_ratio.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@delivery_call_verification_ratio_index')->name('index');
            Route::post('update', 'Admins\GlobalSettingsController@delivery_call_verification_ratio_update')->name('update');
        });

        Route::prefix('stock_movement')->name('stock_movement.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@stock_movement_index')->name('index');
            Route::post('update', 'Admins\GlobalSettingsController@stock_movement_update')->name('update');
        });

        Route::prefix('crm_cut_off_time_and_holidays')->name('crm_cut_off_time_and_holidays.')->group(function () {
            Route::get('', 'Admins\AdminCrmSettingsController@crm_cut_off_time_and_holidays_index')->name('index');
            Route::post('update', 'Admins\AdminCrmSettingsController@crm_cut_off_time_and_holidays_update')->name('update');
            Route::post('list', 'Admins\AdminCrmSettingsController@crm_cut_off_time_and_holidays_list')->name('list');
            Route::post('add', 'Admins\AdminCrmSettingsController@crm_cut_off_time_and_holidays_add')->name('add');
        });

        Route::prefix('return_confirmation_pending_shipment_selection_time')->name('return_confirmation_pending_shipment_selection_time.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@return_confirmation_pending_shipment_selection_time_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@return_confirmation_pending_shipment_selection_time_store')->name('store');
        });
		Route::prefix('consolidation')->name('consolidation.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@consolidation_max_shipments_index')->name('max.index');
            Route::post('update', 'Admins\GlobalSettingsController@consolidation_max_shipments_update')->name('max.update');
        });

        Route::prefix('crm_case_nature_types')->name('crm_case_nature_types.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@crm_case_nature_types_index')->name('index');
            Route::post('list', 'Admins\GlobalSettingsController@crm_case_nature_types_list')->name('list');
            Route::post('status', 'Admins\GlobalSettingsController@crm_case_nature_types_status')->name('status');
            Route::post('store', 'Admins\GlobalSettingsController@crm_case_nature_types_store')->name('store');
        });

        Route::prefix('return_delivered_to_shipper_email_cut_off_time')->name('return_delivered_to_shipper_email_cut_off_time.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@return_delivered_to_shipper_email_cut_off_time_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@return_delivered_to_shipper_email_cut_off_time_store')->name('store');
        });

        Route::prefix('crm_reopen')->name('crm_reopen.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@crm_reopen_count_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@crm_reopen_count_submit')->name('update');
        });

        Route::prefix('multiple_sale_tagging')->name('multiple_sale_tagging.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@multiple_sale_tagging_index')->name('index');
            Route::post('list', 'Admins\GlobalSettingsController@multiple_sale_tagging_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@multiple_sale_tagging_submit')->name('submit');
            Route::post('assign_admin/view', 'Admins\GlobalSettingsController@multiple_sale_tagging_assign_view')->name('assign_admin.view');
            Route::post('assign_admin/submit', 'Admins\GlobalSettingsController@multiple_sale_tagging_assign_submit')->name('assign_admin.submit');
            Route::post('assign_admin/view_assigned', 'Admins\GlobalSettingsController@multiple_sale_tagging_assign_view_assigned')->name('assign_admin.view_assigned');
        });

        Route::prefix('foc_account')->name('foc_account.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@foc_account_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@foc_account_store')->name('store');
        });

        Route::prefix('invoice_against_return_delivered_shipper')->name('invoice_against_return_delivered_shipper.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@invoice_against_return_delivered_shipper_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@invoice_against_return_delivered_shipper_store')->name('store');
//            Route::get('test', 'Admins\AdminFinanceController@generate_reimbursement_invoice')->name('test');//todo:for debugging the function.
        });

        Route::prefix('ccd_booking')->name('ccd_booking.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@ccd_booking_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@ccd_booking_store')->name('store');
        });

        Route::prefix('nsa_account')->name('nsa_account.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@nsa_account_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@nsa_account_store')->name('store');
        });

        Route::prefix('carrefour_account')->name('carrefour_account.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@carrefour_account_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@carrefour_account_store')->name('store');
        });

        Route::prefix('restrict_cities_intercept')->name('restrict_cities_intercept.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@restrict_cities_intercept_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@restrict_cities_intercept_store')->name('store');
        });

        Route::prefix('minimum_chargeable_weight')->name('minimum_chargeable_weight.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@minimum_chargeable_weight_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@minimum_chargeable_weight_update')->name('update');
        });

        Route::prefix('sales')->name('sales.')->group(function () {
            Route::prefix('incentive')->name('incentive.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@sales_incentive')->name('index');
            Route::post('add', 'Admins\GlobalSettingsController@sales_incentive_add')->name('add');
            });
            Route::prefix('targets')->name('targets.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@sales_person_targets')->name('index');
                Route::post('', 'Admins\GlobalSettingsController@sales_person_targets_submit')->name('update');
                Route::get('list', 'Admins\GlobalSettingsController@sales_person_targets_list')->name('list');

            });
            Route::prefix('history')->name('history.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@sales_person_targets_history')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@sales_person_targets_history_list')->name('list');
            });

            Route::prefix('key_accounts')->name('key_accounts.')->group(function () {
                Route::get('', 'Admins\AdminSalesController@key_accounts_dashboard_index')->name('dashboard');
                Route::post('details', 'Admins\AdminSalesController@key_accounts_dashboard_details')->name('dashboard.details');
            });

            Route::prefix('user_restriction')->name('user_restriction.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@sales_user_restriction_index')->name('index');
                Route::post('store', 'Admins\GlobalSettingsController@sales_user_restriction_store')->name('store');
            });


            Route::prefix('projection')->name('projection.')->group(function () {
                Route::prefix('percentage')->name('percentage.')->group(function () {
                    Route::get('', 'Admins\GlobalSettingsController@projection_percentage_index')->name('index');
                    Route::post('', 'Admins\GlobalSettingsController@projection_percentage_update')->name('store');
                });
                Route::prefix('reasons')->name('reasons.')->group(function () {
                    Route::get('', 'Admins\GlobalSettingsController@projection_reason_index')->name('index');
                    Route::get('list', 'Admins\GlobalSettingsController@projection_reason_list')->name('list');
                    Route::post('', 'Admins\GlobalSettingsController@projection_reason_update')->name('store');
                });

                Route::prefix('shipments')->name('shipments.')->group(function () {
                    Route::get('', 'Admins\GlobalSettingsController@projection_shipments_index')->name('index');
                    Route::post('', 'Admins\GlobalSettingsController@projection_shipments_update')->name('store');
                    Route::get('list', 'Admins\GlobalSettingsController@projection_shipments_list')->name('list');
                });

            });
        });


        Route::prefix('aging_report')->name('aging_report.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@completed_aging_report_settings_index')->name('index');
            Route::post('aging_settings_store', 'Admins\GlobalSettingsController@completed_aging_report_settings_store')->name('aging_settings_store');
        });

        Route::prefix('zero_charges')->name('zero_charges.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@zero_charges_report_settings_index')->name('index');
            Route::post('zero_charges_store', 'Admins\GlobalSettingsController@zero_charges_report_settings_store')->name('zero_charges_store');
        });

        Route::prefix('overnight_overland_cargo_report')->name('overnight_overland_cargo_report.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@overnight_overland_cargo_report_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@overnight_overland_cargo_report_list')->name('list');
            Route::post('update', 'Admins\GlobalSettingsController@overnight_overland_cargo_report_rad_tat_submit')->name('update');
            Route::get('edit/{id}', 'Admins\GlobalSettingsController@overnight_overland_cargo_report_edit_index')->name('edit');
            Route::post('edit/update', 'Admins\GlobalSettingsController@overnight_overland_cargo_report_origin_submit')->name('edit.update');
        });

        Route::prefix('delay_in_delivery_massage')->name('delay_in_delivery_massage.')->group(function (){
            Route::get('','Admins\GlobalSettingsController@delay_in_delivery_massage')->name('index');
            Route::post('submit','Admins\GlobalSettingsController@delay_in_delivery_massage_store')->name('store');
        });

        Route::prefix('crm_comment')->name('crm_comment.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@auto_crm_comment_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@auto_crm_comment_store')->name('store');
        });

        Route::prefix('default_agent')->name('default_agent.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@crm_default_agent_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@crm_default_agent_store')->name('store');
        });

        Route::prefix('auto_assigning')->name('auto_assigning.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@crm_auto_assigning_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@crm_auto_assigning_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@crm_auto_assigning_submit')->name('submit');
            Route::post('data', 'Admins\GlobalSettingsController@crm_auto_assigning_data')->name('data');
            Route::post('update', 'Admins\GlobalSettingsController@crm_auto_assigning_update')->name('update');
            Route::post('delete', 'Admins\GlobalSettingsController@crm_auto_assigning_delete')->name('delete');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@crm_auto_assigning_enable_disable')->name('enable_disable');

        });

        Route::prefix('auto_tagging')->name('auto_tagging.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@crm_auto_tagging_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@crm_auto_tagging_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@crm_auto_tagging_submit')->name('submit');
            Route::post('data', 'Admins\GlobalSettingsController@crm_auto_tagging_data')->name('data');
            Route::post('update', 'Admins\GlobalSettingsController@crm_auto_tagging_update')->name('update');
            Route::post('delete', 'Admins\GlobalSettingsController@crm_auto_tagging_delete')->name('delete');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@crm_auto_tagging_enable_disable')->name('enable_disable');

        });

        Route::prefix('blacklist')->name('blacklist.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@blacklist_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@blacklist_list')->name('list');
            Route::get('add', 'Admins\GlobalSettingsController@blacklist_add')->name('add');
            Route::post('add', 'Admins\GlobalSettingsController@blacklist_add_store')->name('add');
            Route::post('unique', 'Admins\GlobalSettingsController@blacklist_unique_criteria')->name('unique');
            Route::post('status', 'Admins\GlobalSettingsController@blacklist_status')->name('status');
            Route::get('edit/{id}','Admins\GlobalSettingsController@blacklist_edit')->name('edit');
            Route::post('edit/{id}','Admins\GlobalSettingsController@blacklist_edit_submit')->name('edit');
            Route::prefix('search')->name('search.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@blacklist_search_index')->name('index');
                Route::post('consignee', 'Admins\GlobalSettingsController@blacklist_search_consignee')->name('consignee');
                Route::post('update', 'Admins\GlobalSettingsController@blacklist_search_update')->name('update');
            });
        });

        //commission routes
        Route::prefix('commission')->name('commission.')->group(function () {
            Route::get('', 'Admins\AdminCommissionController@index')->name('index');
            Route::get('list', 'Admins\AdminCommissionController@tier_list')->name('list');
            Route::post('add', 'Admins\AdminCommissionController@add_sales_tier')->name('add');
            Route::post('status', 'Admins\AdminCommissionController@commission_status')->name('status');
            Route::post('details','Admins\AdminCommissionController@editSalesTierView')->name('details');
            Route::post('edit','Admins\AdminCommissionController@editSalesTier')->name('edit');
            Route::get('set_commission/{id?}','Admins\AdminCommissionController@setCommission')->name('set_commission');
            Route::post('set_commission','Admins\AdminCommissionController@set_commission_submit')->name('set_commission.submit');
            Route::get('approve_commission/{id?}','Admins\AdminCommissionController@approveCommission')->name('approve_commission');
            Route::post('approve_commission','Admins\AdminCommissionController@approve_commission_submit')->name('approve_commission.submit');
            Route::prefix('percentage')->name('percentage.')->group(function () {
                    Route::get('', 'Admins\GlobalSettingsController@commission_percentage_index')->name('index');
                    Route::post('', 'Admins\GlobalSettingsController@commission_percentage_update')->name('store');
                });
        });


        Route::prefix('return')->name('return.')->group(function () {
            Route::prefix('reason')->name('reason.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@return_reason_index')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@return_reason_list')->name('list');
                Route::post('add', 'Admins\GlobalSettingsController@return_reason_add')->name('add');
                Route::post('get', 'Admins\GlobalSettingsController@return_reason_get')->name('get');
                Route::post('edit', 'Admins\GlobalSettingsController@return_reason_edit')->name('edit');
            });
        });

        Route::prefix('escalation')->name('escalation.')->group(function () {
            Route::prefix('launched')->name('launched.')->group(function () {
                Route::get('', 'Admins\AdminCrmSettingsController@escalation_launched_index')->name('index');
                Route::get('/list', 'Admins\AdminCrmSettingsController@escalation_launched_list')->name('list');
                Route::get('/add', 'Admins\AdminCrmSettingsController@escalation_launched_add_index')->name('add.index');
                Route::post('/add/store', 'Admins\AdminCrmSettingsController@escalation_launched_add_store')->name('add.store');
                Route::get('/edit/{id}', 'Admins\AdminCrmSettingsController@escalation_launched_edit_index')->name('edit.index');
                Route::post('/edit/store', 'Admins\AdminCrmSettingsController@escalation_launched_edit_store')->name('edit.store');
            });
            Route::prefix('in_process')->name('in_process.')->group(function () {
                Route::get('', 'Admins\AdminCrmSettingsController@escalation_in_process_index')->name('index');
                Route::get('/list', 'Admins\AdminCrmSettingsController@escalation_in_process_list')->name('list');
                Route::get('/add', 'Admins\AdminCrmSettingsController@escalation_in_process_add_index')->name('add.index');
                Route::post('/add/store', 'Admins\AdminCrmSettingsController@escalation_in_process_add_store')->name('add.store');
                Route::get('/edit/{id}', 'Admins\AdminCrmSettingsController@escalation_in_process_edit_index')->name('edit.index');
                Route::post('/edit/store', 'Admins\AdminCrmSettingsController@escalation_in_process_edit_store')->name('edit.store');
            });
            Route::prefix('tagging')->name('tagging.')->group(function () {
                Route::get('', 'Admins\AdminCrmSettingsController@escalation_tagging_index')->name('index');
                Route::get('/list', 'Admins\AdminCrmSettingsController@escalation_tagging_list')->name('list');
                Route::get('/add', 'Admins\AdminCrmSettingsController@escalation_tagging_add_index')->name('add.index');
                Route::post('/add/store', 'Admins\AdminCrmSettingsController@escalation_tagging_add_store')->name('add.store');
                Route::get('/edit/{id}', 'Admins\AdminCrmSettingsController@escalation_tagging_edit_index')->name('edit.index');
                Route::post('/edit/store', 'Admins\AdminCrmSettingsController@escalation_tagging_edit_store')->name('edit.store');
                Route::post('/status', 'Admins\AdminCrmSettingsController@escalation_tagging_status_update')->name('status');
                Route::post('/view_hubs', 'Admins\AdminCrmSettingsController@escalation_tagging_view_hubs')->name('view_hubs');
                Route::post('/view_statuses', 'Admins\AdminCrmSettingsController@escalation_tagging_view_statuses')->name('view_statuses');
                Route::post('/view_levels', 'Admins\AdminCrmSettingsController@escalation_tagging_view_levels')->name('view_levels');
            });
            Route::post('/status', 'Admins\AdminCrmSettingsController@escalation_status_update')->name('status');
            Route::post('/view_statuses', 'Admins\AdminCrmSettingsController@escalation_view_statuses')->name('view_statuses');
            Route::get('/levels', 'Admins\AdminCrmSettingsController@escalation_level_index')->name('levels.index');
            Route::post('/levels/store', 'Admins\AdminCrmSettingsController@escalation_level_store')->name('levels.store');
        });


        Route::prefix('holidays')->name('holidays.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@holidays_index')->name('index');
            Route::post('update', 'Admins\GlobalSettingsController@holidays_update')->name('update');
            Route::post('list', 'Admins\GlobalSettingsController@holidays_list')->name('list');
            Route::post('add', 'Admins\GlobalSettingsController@holidays_add')->name('add');
        });

        Route::prefix('not_attempted_cron')->name('not_attempted_cron.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@not_attempted_cron_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@not_attempted_cron_store')->name('store');
        });
		Route::prefix('station_recovery_cron')->name('station_recovery_cron.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@station_recovery_cron_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@station_recovery_cron_store')->name('store');
        });

        Route::prefix('over_payment_limit')->name('over_payment_limit.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@over_payment_limit_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@over_payment_limit_store')->name('store');
        });
		Route::prefix('short_received_hub_wise_cron')->name('short_received_hub_wise_cron.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@short_received_hub_wise_cron_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@short_received_hub_wise_cron_store')->name('store');
        });

        Route::prefix('restrict_parcels_attempt')->name('restrict_parcels_attempt.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@restrict_parcels_attempt_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@restrict_parcels_attempt_list')->name('list');
            Route::post('add', 'Admins\GlobalSettingsController@restrict_parcels_attempt_add')->name('add');
            Route::post('edit', 'Admins\GlobalSettingsController@restrict_parcels_attempt_edit')->name('edit');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@restrict_parcels_attempt_enable_disable')->name('enable_disable');
        });

        Route::prefix('runner')->name('runner.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@runner_report_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@runner_report_list')->name('list');
            Route::get('unique', 'Admins\GlobalSettingsController@runner_report_unique')->name('unique');
            Route::post('add', 'Admins\GlobalSettingsController@runner_report_add')->name('add');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@runner_report_enable_disable')->name('enable_disable');
        });
		Route::prefix('sms_shipper_wise')->name('sms_shipper_wise.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@arrived_at_origin_sms_for_shipper_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@arrived_at_origin_sms_for_shipper_update')->name('update');
        });
        Route::prefix('pickup_address_wise_payment_accounts')->name('pickup_address_wise_payment_accounts.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@pickup_address_wise_payment_accounts_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@pickup_address_wise_payment_accounts_submit')->name('store');
        });

        Route::prefix('month_closing')->name('month_closing.')->group(function () {
            Route::prefix('types')->name('types.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@month_closing_type_index')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@month_closing_type_list')->name('list');
                Route::post('add', 'Admins\GlobalSettingsController@month_closing_type_add')->name('add');
                Route::post('edit', 'Admins\GlobalSettingsController@month_closing_type_edit')->name('edit');
            });
            Route::prefix('status')->name('status.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@month_closing_status_index')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@month_closing_status_list')->name('list');
                Route::post('add', 'Admins\GlobalSettingsController@month_closing_status_add')->name('add');
                Route::post('edit', 'Admins\GlobalSettingsController@month_closing_status_edit')->name('edit');
            });
        });

        Route::prefix('international_rates')->name('international_rates.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@international_rates_index')->name('index');
            Route::post('update', 'Admins\GlobalSettingsController@international_rates_update')->name('update');

            Route::prefix('upload')->name('upload.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@international_rates_upload_index')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@international_standard_dhl_rates_list')->name('list');
                Route::post('excel', 'Admins\GlobalSettingsController@international_rates_upload_excel')->name('excel');

            });
        });

        Route::prefix('hr')->name('hr.')->group(function () {
            Route::prefix('rider_incentive')->name('rider_incentive.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@rider_incentive_index')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@rider_incentive_list')->name('list');
                Route::post('store', 'Admins\GlobalSettingsController@rider_incentive_store')->name('store');
                Route::get('details', 'Admins\GlobalSettingsController@rider_incentive_details')->name('details');
                Route::post('update', 'Admins\GlobalSettingsController@rider_incentive_update')->name('update');

                Route::prefix('cron')->name('cron.')->group(function () {
                    Route::get('', 'Admins\GlobalSettingsController@rider_incentive_cron_index')->name('index');
                    Route::post('store', 'Admins\GlobalSettingsController@rider_incentive_cron_store')->name('store');

                });
            });
        });

		Route::prefix('return_confirmation_pending_tat_setting')->name('rcp_tat.')->group(function () {
            Route::get('','Admins\GlobalSettingsController@rcp_tat_index')->name('index');
            Route::get('list','Admins\GlobalSettingsController@rcp_tat_list')->name('list');
            Route::put('update','Admins\GlobalSettingsController@rcp_tat_update')->name('update');
        });

        Route::prefix('return_confirmation_pending_sms_setting')->name('rcp_sms.')->group(function () {
            Route::get('','Admins\GlobalSettingsController@rcp_sms_index')->name('index');
            Route::post('update','Admins\GlobalSettingsController@rcp_sms_update')->name('update');


//            Route::get('','Admins\GlobalSettingsController@rcp_sms_cron_index')->name('cron_index');
//            Route::post('update','Admins\GlobalSettingsController@rcp_sms_cron_update')->name('cron_update');
        });

        Route::prefix('debriefing_time_setting')->name('debriefing_time_setting.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@debriefing_time_setting_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@debriefing_time_setting_update')->name('update');
        });

        Route::prefix('omni')->name('omni.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@omni_user_setting_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@omni_user_setting_update')->name('update');
        });

        Route::prefix('shipment_status_eta')->name('shipment_status_eta.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@shipment_status_eta_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@shipment_status_eta_list')->name('list');
            Route::post('store', 'Admins\GlobalSettingsController@shipment_status_eta_store')->name('store');
            Route::post('edit', 'Admins\GlobalSettingsController@shipment_status_eta_edit')->name('edit');
        });

        Route::prefix('rider_shipment_attempt')->name('rider_shipment_attempt.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@rider_shipment_attempt_settings_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@rider_shipment_attempt_settings_store')->name('store');
        });

        Route::prefix('rider_deactivation_cron')->name('rider_deactivation_cron.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@rider_deactivation_cron_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@rider_deactivation_cron_store')->name('store');
        });

        Route::prefix('bolt_update_version')->name('bolt_update_version.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@bolt_update_version_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@bolt_update_version_store')->name('store');
        });

		Route::prefix('last_mile_cron')->name('last_mile_cron.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@last_mile_cron_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@last_mile_cron_store')->name('store');
        });

		Route::prefix('dhl_sync_time')->name('dhl_sync_time.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@dhl_sync_time_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@dhl_sync_time_store')->name('store');
        });

		Route::prefix('international_automation_user')->name('international_automation_user.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@international_automation_user_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@international_automation_user_store')->name('store');
        });

        Route::prefix('reattempt_percentage')->name('reattempt_percentage.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@reattempt_percentage_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@reattempt_percentage_store')->name('store');
        });

        Route::prefix('lead_tagging')->name('lead_tagging.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@lead_tagging_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@lead_tagging_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@lead_tagging_submit')->name('submit');
            Route::post('data', 'Admins\GlobalSettingsController@lead_tagging_data')->name('data');
            Route::post('update', 'Admins\GlobalSettingsController@lead_tagging_update')->name('update');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@lead_tagging_enable_disable')->name('enable_disable');

        });

        Route::prefix('lead_zones')->name('lead_zones.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@lead_zones_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@lead_zones_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@lead_zones_submit')->name('submit');
            Route::post('data', 'Admins\GlobalSettingsController@lead_zones_data')->name('data');
            Route::post('update', 'Admins\GlobalSettingsController@lead_zones_update')->name('update');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@lead_zones_enable_disable')->name('enable_disable');

        });

        Route::prefix('lead_notification')->name('lead_notification.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@lead_notification_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@lead_notification_list')->name('list');
            Route::post('data', 'Admins\GlobalSettingsController@lead_notification_data')->name('data');
            Route::post('update', 'Admins\GlobalSettingsController@lead_notification_update')->name('update');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@lead_notification_enable_disable')->name('enable_disable');
            Route::post('delete_image', 'Admins\GlobalSettingsController@lead_notification_delete_image')->name('delete_image');
        });

        Route::prefix('shippers_origin_change')->name('shippers_origin_change.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@shipper_origin_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@shipper_origin_store')->name('store');
        });

        Route::prefix('shippers_return_address')->name('shippers_return_address.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@shippers_return_address_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@shippers_return_address_store')->name('store');
        });

        Route::prefix('consignee_sms_expire')->name('consignee_sms_expire.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@consignee_sms_expire_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@consignee_sms_expire_store')->name('store');
        });

        Route::prefix('return_reason_mandatory')->name('return_reason_mandatory.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@return_reason_mandatory_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@return_reason_mandatory_list')->name('list');
            Route::post('store', 'Admins\GlobalSettingsController@return_reason_mandatory_store')->name('store');
//            Route::get('cn_print_right', 'Admins\GlobalSettingsController@cn_print_right')->name('cn_print_right');
        });

        Route::prefix('return_shipments_address')->name('return_shipments_address.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@return_shipments_address_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@return_shipments_address_store')->name('store');
        });

        Route::prefix('auto_tag_territories')->name('auto_tag_territories.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@auto_tag_territories_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@auto_tag_territories_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@auto_tag_territories_store')->name('submit');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@auto_tag_territories_enable_disable')->name('enable_disable');
            Route::post('data', 'Admins\GlobalSettingsController@auto_tag_territories_data')->name('data');
            Route::post('update', 'Admins\GlobalSettingsController@auto_tag_territories_update')->name('update');
        
        });
		Route::prefix('cn_print_right')->name('cn_print_right.')->group(function () {
			Route::get('', 'Admins\GlobalSettingsController@cn_print_right')->name('cn_print_right');
			Route::post('store', 'Admins\GlobalSettingsController@cn_print_right_store')->name('store');
		});
        Route::prefix('referral')->name('referral.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@referral')->name('index');
			Route::get('list', 'Admins\GlobalSettingsController@referral_list')->name('list');
			Route::post('submit', 'Admins\GlobalSettingsController@referral_store')->name('submit');
            Route::get('name', 'Admins\GlobalSettingsController@referral_name')->name('name');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@referral_enable_disable')->name('enable_disable');
            

        });

        Route::prefix('lost_shipment_shippers')->name('lost_shipment_shippers.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@lost_shipment_shippers_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@lost_shipment_shippers_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@lost_shipment_shippers_add')->name('add');
            Route::post('delete', 'Admins\GlobalSettingsController@lost_shipment_shippers_delete')->name('delete');

        });
        Route::prefix('lost_shipment_admins')->name('lost_shipment_admins.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@lost_shipment_admins_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@lost_shipment_admins_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@lost_shipment_admins_add')->name('add');
            Route::post('delete', 'Admins\GlobalSettingsController@lost_shipment_admins_delete')->name('delete');

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
            Route::post('check_quantity', 'Admins\AdminWalkInBookShipmentController@check_packing_quantity')->name('check_quantity');
            Route::get('international', 'Admins\AdminWalkInBookShipmentController@international_book_index')->name('international_walk_in');
            Route::post('international_store', 'Admins\AdminWalkInBookShipmentController@international_walk_in_store')->name('international_store');
            Route::post('check_international_min_charges', 'Admins\AdminWalkInBookShipmentController@check_international_min_charges')->name('check_international_min_charges');
            Route::post('check_international_standard_weight', 'Admins\AdminWalkInBookShipmentController@check_international_standard_weight')->name('check_international_standard_weight');

            Route::prefix('ftl')->name('ftl.')->group(function () {
                Route::get('', 'Admins\AdminWalkInBookShipmentController@ftl_book_index')->name('walk_in');
                Route::post('', 'Admins\AdminWalkInBookShipmentController@get_ftl_info')->name('get_ftl_info');
                Route::post('store', 'Admins\AdminWalkInBookShipmentController@ftl_store')->name('store');
                Route::post('print_ftl_air_waybill', 'Admins\AdminWalkInBookShipmentController@print_ftl_air_waybill')->name('print_air_waybill');
            });

        });
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('', 'Admins\AdminWalkInBookShipmentController@history_index')->name('walk_in_history');
            Route::get('list', 'Admins\AdminWalkInBookShipmentController@history_list')->name('walk_in_history_list');
        });
		Route::prefix('consolidation')->name('consolidation.')->group(function () {
            Route::prefix('history')->name('history.')->group(function () {
                Route::get('', 'Admins\AdminConsolidatedController@consolidation_history_index')->name('index');
                Route::get('list', 'Admins\AdminConsolidatedController@consolidation_history_list')->name('list');
            });
        });
        Route::prefix('receiving_sheet')->name('receiving_sheet.')->group(function () {
                Route::get('', 'Admins\AdminReceivingSheetHistoryController@receiving_sheet_index')->name('index');
            Route::get('list', 'Admins\AdminReceivingSheetHistoryController@receiving_sheet_list')->name('list');
            Route::post('print', 'Admins\AdminReceivingSheetHistoryController@print')->name('print');
        });
        Route::prefix('poc_kam_tagged_accounts')->name('poc_kam_tagged_accounts.')->group(function () {
            Route::get('', 'Admins\AdminTaggedAccountsController@index')->name('index');
            Route::get('list', 'Admins\AdminTaggedAccountsController@list')->name('list');
        });

    });

    //CMC Routes
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::prefix('request')->name('request.')->group(function(){
            Route::post('add', 'Admins\AdminCRMController@add_request')->name('add');
//            Route::post('get_request', 'Admins\AdminCRMController@get_request_info')->name('get_request');
//            Route::post('update', 'Admins\AdminCRMController@update_request')->name('update');
            Route::get('', 'Admins\AdminCRMController@launched')->name('launched');
            Route::get('{id}', 'Admins\AdminCRMController@request_details')->name('details');
            Route::post('edit', 'Admins\AdminCRMController@edit_request')->name('edit');
            Route::post('image_details','Admins\AdminCRMController@crm_image_details')->name('image_details');
            Route::post('image_submit','Admins\AdminCRMController@crm_image_submit')->name('image_submit');
            Route::post('image_delete','Admins\AdminCRMController@crm_image_delete')->name('image_delete');
            Route::post('/lost/claim', 'Admins\AdminCRMController@lost_claim')->name('lost.claim');
            Route::post('request', 'Admins\AdminCRMController@special_request_appvove')->name('special_request_appvove');
            Route::post('request_adjusted', 'Admins\AdminCRMController@special_request_adjusted')->name('special_request_adjusted');

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
            Route::post('list', 'Admins\AdminCRMController@in_process_list')->name('list');
            Route::post('tag', 'Admins\AdminCRMController@bulk_admin_tag')->name('tag');
            Route::post('un_tag', 'Admins\AdminCRMController@admin_un_tag')->name('un_tag');
            Route::post('special_request_tag', 'Admins\AdminCRMController@special_request_tag')->name('special_request_tag');
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
        Route::post('bulk_re_open', 'Admins\AdminCRMController@bulk_re_open')->name('bulk_re_open');
        Route::post('invalid', 'Admins\AdminCRMController@invalid')->name('invalid');
        Route::post('tag', 'Admins\AdminCRMController@admin_tag')->name('tag');
        Route::prefix('comment')->name('comment.')->group(function(){
            Route::post('add', 'Admins\AdminCRMController@add_comment')->name('add');
            Route::post('get', 'Admins\AdminCRMController@get_latest_comment')->name('get');
            Route::post('edit', 'Admins\AdminCRMController@edit_comment')->name('edit');
            Route::post('bulk', 'Admins\AdminCRMController@bulk_comment_for_shipper')->name('bulk');
        });
        Route::post('escalation_status', 'Admins\AdminCRMController@escalation_status')->name('escalation_status');
        Route::post('escalate', 'Admins\AdminCRMController@escalate')->name('escalate');
        Route::get('permissions', 'Admins\AdminCRMController@crm_index')->name('permissions');
        Route::get('list', 'Admins\AdminCRMController@crm_list')->name('list');

        Route::prefix('update/{id}')->name('update.')->group(function() {
            Route::get('', 'Admins\AdminCRMController@crm_update_index')->name('index');
            Route::post('', 'Admins\AdminCRMController@crm_update_store')->name('store');
        });
        Route::post('bulk_valid_invalid', 'Admins\AdminCRMController@bulk_valid_invalid')->name('bulk_valid_invalid');
        Route::prefix('claim')->name('claim.')->group(function(){
            Route::get('product_image/{id}', 'Admins\AdminCRMController@product_image')->name('product_image');
            Route::get('invoice_image/{id}', 'Admins\AdminCRMController@invoice_image')->name('invoice_image');
            Route::get('damage_product_image/{id}', 'Admins\AdminCRMController@damage_product_image')->name('damage_product_image');
            Route::get('product_packaging_image/{id}', 'Admins\AdminCRMController@product_packaging_image')->name('product_packaging_image');
            Route::get('actual_product_image/{id}', 'Admins\AdminCRMController@actual_product_image')->name('actual_product_image');
            Route::get('missing_product_image/{id}', 'Admins\AdminCRMController@missing_product_image')->name('missing_product_image');
            Route::get('product_packaging_image_for_content_short/{id}', 'Admins\AdminCRMController@product_packaging_image_for_content_short')->name('product_packaging_image_for_content_short');
            Route::get('actual_product_image_for_content_short/{id}', 'Admins\AdminCRMController@actual_product_image_for_content_short')->name('actual_product_image_for_content_short');
        });
        Route::prefix('consignee_info')->name('consignee_info.')->group(function(){
            Route::get('', 'Admins\AdminCRMController@consignee_info_index')->name('index');
            Route::get('list', 'Admins\AdminCRMController@consignee_info_list')->name('list');
            Route::post('print_air_waybill', 'Admins\AdminCRMController@print_air_waybill')->name('print_air_waybill');
            Route::post('resolve', 'Admins\AdminCRMController@consignee_info_resolve')->name('resolve');
        });
    });

    Route::prefix('intercept')->name('intercept.')->group(function (){
        Route::get('/{row_id}','Admins\AdminInterceptRebookRequestHistoryController@intercept_re_book_index')->name('index');
        Route::post('update','Admins\AdminInterceptRebookRequestHistoryController@intercept_re_book_update')->name('update');
        Route::post('get_express_centers', 'Admins\AdminInterceptRebookRequestHistoryController@get_express_centers')->name('get_express_centers');

    });

    Route::prefix('resources')->name('resources.')->group(function (){
        Route::get('','Admins\AdminResourcesController@index')->name('index');
        Route::get('city_list','Admins\AdminResourcesController@get_network_list')->name('city_list');
    });
    Route::prefix('scanning_history')->name('scanning_history.')->group(function (){
        Route::get('','Admins\AdminShipmentScanningHistoryController@index')->name('index');
        Route::post('details','Admins\AdminShipmentScanningHistoryController@details')->name('details');
    });
    Route::prefix('airway_journey')->name('airway_journey.')->group(function (){
        Route::get('','AdminAirwayBillJournyController@index')->name('index');
        Route::post('details','AdminAirwayBillJournyController@details')->name('details');
    });

    Route::prefix('coordinates')->name('coordinates.')->group(function (){
        Route::prefix('add')->name('add.')->group(function (){
            Route::get('','Admins\CoordinatesController@add_index')->name('index');
            Route::post('submit','Admins\CoordinatesController@add_submit')->name('submit');
            Route::post('details','Admins\CoordinatesController@shipment_details')->name('shipment_details');
            Route::post('search','Admins\CoordinatesController@address_search')->name('search.address');
        });
    });

    Route::prefix('handover')->name('handover.')->group(function () {
        Route::prefix('create')->name('create.')->group(function () {
            Route::get('', 'AdminShipmentHandoverController@handover_create_index')->name('index');
            Route::post('fetch', 'AdminShipmentHandoverController@handover_dropdown_val_fetch_from')->name('fetch');
            Route::post('fetch1', 'AdminShipmentHandoverController@handover_dropdown_val_fetch_to')->name('fetch1');
            Route::post('shipment_details', 'AdminShipmentHandoverController@arrival_bulk_shipment_details')->name('shipment_details');
            Route::post('store', 'AdminShipmentHandoverController@bulk_handover_submit')->name('store');
        });
        Route::prefix('receive')->name('receive.')->group(function () {
            Route::get('', 'AdminShipmentHandoverController@handover_receive_index')->name('index');
            Route::post('shipment_details', 'AdminShipmentHandoverController@arrival_bulk_shipment_details_receive')->name('shipment_details');
            Route::post('store', 'AdminShipmentHandoverController@bulk_handover_submit_receive')->name('store');
        });
        Route::prefix('list')->name('list.')->group(function () {
            Route::get('', 'AdminShipmentHandoverController@handover_list_index')->name('index');
            Route::get('list', 'AdminShipmentHandoverController@handover_list')->name('list');
            Route::post('shipments','AdminShipmentHandoverController@handover_shipments_count')->name('shipments');
            Route::put('delivered','AdminShipmentHandoverController@handover_shipments_delivered')->name('delivered');
            Route::post('print','AdminShipmentHandoverController@handover_print')->name('print');
        });
        Route::prefix('responsibles')->name('responsibles.')->group(function () {
            Route::get('', 'AdminShipmentHandoverController@responsibles_index')->name('index');
            Route::get('list', 'AdminShipmentHandoverController@responsibles_list')->name('list');
            Route::post('add', 'AdminShipmentHandoverController@responsibles_add')->name('add');
            Route::post('status', 'AdminShipmentHandoverController@responsibles_status')->name('status');
            Route::post('details','AdminShipmentHandoverController@responsibles_editview')->name('details');
            Route::post('edit','AdminShipmentHandoverController@responsibles_edit')->name('edit');
        });
    });
    Route::prefix('power_bi')->name('power_bi.')->group(function () {
        Route::get('sales', 'Admins\AdminPowerBIController@sales_dashboard_index')->name('sales');
        Route::get('operation', 'Admins\AdminPowerBIController@operation_dashboard_index')->name('operation');
    });
    //Arrival Service Center
    Route::prefix('arrival_service')->name('arrival_service.')->group(function () {
        Route::get('', 'Admins\V2Pickup\V2AdminArrivalServiceController@arrival_service_index')->name('index');
        Route::post('shipment_details', 'Admins\V2Pickup\V2AdminArrivalServiceController@arrival_service_details')->name('shipment_details');
        Route::prefix('try_and_buy')->name('try_and_buy.')->group(function () {
            Route::post('item_details', 'Admins\AdminPickupsController@try_and_buy_item_details')->name('item_details');
            Route::post('shipment_details', 'Admins\V2Pickup\V2AdminArrivalServiceController@arrival_try_and_buy_shipment_details')->name('shipment_details');
        });
    });

    Route::prefix('runner')->name('runner.')->group(function () {
        Route::get('', 'Admins\AdminRunnerController@index')->name('index');
        Route::get('list', 'Admins\AdminRunnerController@list')->name('list');
        Route::get('add', 'Admins\AdminRunnerController@runner_details_add_index')->name('add');
        Route::post('add/submit', 'Admins\AdminRunnerController@runner_details_add_submit')->name('add.submit');
        Route::get('edit/{id?}', 'Admins\AdminRunnerController@runner_details_edit_index')->name('edit');
        Route::post('edit/submit', 'Admins\AdminRunnerController@runner_details_edit_submit')->name('edit.submit');
        Route::post('/view_details', 'Admins\AdminRunnerController@runner_details_view')->name('view_details');
        Route::get('in_transit', 'Admins\AdminRunnerController@vehicle_in_transit')->name('intransit');
        Route::get('in_transit/list', 'Admins\AdminRunnerController@vehicle_in_transit_list')->name('intransit.list');
    });

    Route::prefix('open_parcel_history')->name('open_parcel_history.')->group(function () {
        Route::get('', 'Admins\AdminParcelHistoryController@index')->name('index');
        Route::post('info', 'Admins\AdminParcelHistoryController@shipment_get_info')->name('info');
        Route::post('submit', 'Admins\AdminParcelHistoryController@remarks_submit')->name('submit');
        Route::get('list', 'Admins\AdminParcelHistoryController@list')->name('list');
	});
	Route::prefix('trax_directory')->name('trax_directory.')->group(function () {
        Route::get('', 'Admins\AdminTraxDirectory@index')->name('index');
        Route::get('list', 'Admins\AdminTraxDirectory@list')->name('list');
    });

	Route::prefix('international')->name('international.')->group(function(){
        Route::prefix('tracking_upload')->name('tracking_upload.')->group(function () {
            Route::get('', 'Admins\AdminInternationalShipmentsController@tracking_upload_index')->name('index');
            Route::get('list', 'Admins\AdminInternationalShipmentsController@tracking_upload_list')->name('list');
            Route::post('store', 'Admins\AdminInternationalShipmentsController@tracking_upload_store')->name('store');
            Route::get('edit', 'Admins\AdminInternationalShipmentsController@tracking_upload_edit_info')->name('edit');
            Route::post('edit', 'Admins\AdminInternationalShipmentsController@tracking_upload_edit')->name('edit');
        });
        Route::prefix('shipment_status')->name('shipment_status.')->group(function () {
            Route::get('', 'Admins\AdminInternationalShipmentsController@shipment_status_index')->name('index');
            Route::post('shipment_info', 'Admins\AdminInternationalShipmentsController@get_shipment_info')->name('shipment_info');
            Route::post('update', 'Admins\AdminInternationalShipmentsController@shipment_status_update')->name('update');
            Route::post('updatemodal', 'Admins\AdminInternationalShipmentsController@shipment_status_update_modal')->name('updatemodal');

        });
            Route::prefix('rates')->name('rates.')->group(function () {
            Route::prefix('economy')->name('economy.')->group(function (){
                Route::get('{id}/{view?}','Admins\AdminInternationalRatesController@addEconomyRatesView')->name('create');
                Route::post('{id}','Admins\AdminInternationalRatesController@addEconomyRatesStore')->name('store');
                Route::post('approve/{id}','Admins\AdminInternationalRatesController@addEconomyRatesApprove')->name('approve');
            });
            Route::prefix('view')->name('view.')->group(function () {
                Route::get('{id}','Admins\AdminInternationalRatesController@view_rates_index')->name('index');
            });
            Route::prefix('update')->name('update.')->group(function () {
                Route::get('list/{id}', 'Admins\AdminInternationalRatesController@standard_rates_list')->name('list');
                Route::get('{id}','Admins\AdminInternationalRatesController@update_rates_index')->name('index');
                Route::post('submit','Admins\AdminInternationalRatesController@update_rates_submit')->name('submit');
                Route::post('reject','Admins\AdminInternationalRatesController@rejectReasonSubmit')->name('reject');
                Route::post('get_credit','Admins\AdminInternationalRatesController@get_credit')->name('get_credit');
                Route::post('credit','Admins\AdminInternationalRatesController@credit_update')->name('credit');

            });
        });
    });

	Route::prefix('telenor')->name('telenor.')->group(function(){
        Route::prefix('arrival')->name('arrival.')->group(function () {
            Route::get('', 'Admins\AdminNsaAccountShipmentController@arrival_index')->name('index');
            Route::post('store', 'Admins\AdminNsaAccountShipmentController@arrival_submit')->name('submit');
        });
        Route::prefix('order_id')->name('order_id.')->group(function () {
            Route::get('', 'Admins\AdminNsaAccountShipmentController@order_id_index')->name('index');
            Route::post('store', 'Admins\AdminNsaAccountShipmentController@order_id_submit')->name('submit');
        });
        Route::prefix('delivery')->name('delivery.')->group(function () {
            Route::get('', 'Admins\AdminNsaAccountShipmentController@delivery_index')->name('index');
            Route::post('store', 'Admins\AdminNsaAccountShipmentController@delivery_submit')->name('submit');
        });
        Route::prefix('return')->name('return.')->group(function () {
            Route::get('', 'Admins\AdminNsaAccountShipmentController@return_index')->name('index');
            Route::post('shipment_info', 'Admins\AdminNsaAccountShipmentController@return_shipment_info')->name('shipment_info');
            Route::post('store', 'Admins\AdminNsaAccountShipmentController@return_submit')->name('submit');
            Route::get('bulk_return', 'Admins\AdminNsaAccountShipmentController@bulk_return_index')->name('bulk_return');
            Route::post('bulk_return/submit', 'Admins\AdminNsaAccountShipmentController@bulk_return_submit')->name('bulk_return_submit');
        });


        Route::prefix('revert')->name('revert.')->group(function () {
            Route::get('bulk_revert', 'Admins\AdminNsaAccountShipmentController@bulk_revert_index')->name('bulk_revert');
            Route::post('bulk_revert_submit', 'Admins\AdminNsaAccountShipmentController@bulk_revert_submit')->name('bulk_revert_submit');
        });

        Route::prefix('call')->name('call.')->group(function () {
            Route::get('', 'Admins\AdminTelenorController@telenor_response')->name('index');
            Route::get('store', 'Admins\AdminTelenorController@telenor_response_list')->name('list');
        });
    });

	Route::prefix('carrefour')->name('carrefour.')->group(function(){
        Route::prefix('arrival')->name('arrival.')->group(function () {
            Route::get('', 'Admins\AdminNsaAccountShipmentController@carrefour_arrival_index')->name('index');
            Route::post('store', 'Admins\AdminNsaAccountShipmentController@carrefour_arrival_submit')->name('submit');
        });
        Route::prefix('delivery')->name('delivery.')->group(function () {
            Route::get('', 'Admins\AdminNsaAccountShipmentController@carrefour_delivery_index')->name('index');
            Route::post('store', 'Admins\AdminNsaAccountShipmentController@carrefour_delivery_submit')->name('submit');
        });
        Route::prefix('return')->name('return.')->group(function () {
            Route::get('', 'Admins\AdminNsaAccountShipmentController@carrefour_return_index')->name('index');
            Route::post('store', 'Admins\AdminNsaAccountShipmentController@carrefour_return_submit')->name('submit');
        });
    });

	Route::prefix('leads')->name('leads.')->group(function(){
        Route::get('', 'Admins\LeadManagementController@index')->name('index');
        Route::get('list', 'Admins\LeadManagementController@list')->name('list');
        Route::post('lead_reasons', 'Admins\LeadManagementController@lead_reasons')->name('lead_reasons');
        Route::post('add_status', 'Admins\LeadManagementController@add_status')->name('add_status');
        Route::post('add_bulk_status', 'Admins\LeadManagementController@add_bulk_status')->name('add_bulk_status');
        Route::post('tag_sale_person', 'Admins\LeadManagementController@tag_sale_person_forward_lead')->name('tag_sale_person');
        Route::post('add_remarks', 'Admins\LeadManagementController@add_remarks')->name('add_remarks');
        Route::get('view_remarks/{id}', 'Admins\LeadManagementController@view_remarks_index')->name('view_remarks');
        Route::post('lead_statistics', 'Admins\LeadManagementController@lead_statistics')->name('lead_statistics');
        Route::post('upload_attachment', 'Admins\LeadManagementController@upload_attachment')->name('upload_attachment');
        Route::get('view_attachment/{id}', 'Admins\LeadManagementController@view_attachment')->name('view_attachment');
        Route::post('info', 'Admins\LeadManagementController@info')->name('info');
        Route::post('edit', 'Admins\LeadManagementController@edit')->name('edit');
    });

    Route::prefix('pam_leads')->name('pam_leads.')->group(function(){
        Route::get('', 'Admins\LeadManagementController@pam_index')->name('index');
        Route::get('list', 'Admins\LeadManagementController@pam_list')->name('list');
        Route::post('items', 'Admins\LeadManagementController@pam_items')->name('items');
    });

	Route::prefix('retail')->name('retail.')->group(function(){
        Route::prefix('franchise')->name('franchise.')->group(function(){
            Route::get('', 'Admins\Retail\RetailAdminUserManagementController@franchise_index')->name('index');
            Route::get('list', 'Admins\Retail\RetailAdminUserManagementController@franchise_list')->name('list');
            Route::post('status', 'Admins\Retail\RetailAdminUserManagementController@franchise_enable_disable')->name('status');
            Route::post('add', 'Admins\Retail\RetailAdminUserManagementController@franchise_add')->name('add');
            Route::post('edit', 'Admins\Retail\RetailAdminUserManagementController@franchise_edit')->name('edit');
            Route::get('name', 'Admins\Retail\RetailAdminUserManagementController@franchise_name')->name('name');
        });
        Route::prefix('trax_center')->name('trax_center.')->group(function(){
            Route::get('', 'Admins\Retail\RetailAdminUserManagementController@trax_center_index')->name('index');
            Route::get('list', 'Admins\Retail\RetailAdminUserManagementController@trax_center_list')->name('list');
            Route::post('status', 'Admins\Retail\RetailAdminUserManagementController@trax_center_enable_disable')->name('status');
            Route::post('add', 'Admins\Retail\RetailAdminUserManagementController@trax_center_add')->name('add');
            Route::post('edit', 'Admins\Retail\RetailAdminUserManagementController@trax_center_edit')->name('edit');
            Route::get('name', 'Admins\Retail\RetailAdminUserManagementController@trax_center_name')->name('name');
        });
        Route::prefix('users')->name('users.')->group(function(){
            Route::get('', 'Admins\Retail\RetailAdminUserManagementController@user_index')->name('index');
            Route::get('list', 'Admins\Retail\RetailAdminUserManagementController@user_list')->name('list');
            Route::post('status', 'Admins\Retail\RetailAdminUserManagementController@user_enable_disable')->name('status');
            Route::post('add', 'Admins\Retail\RetailAdminUserManagementController@user_add')->name('add');
            Route::get('edit/{id}', 'Admins\Retail\RetailAdminUserManagementController@user_edit')->name('edit');
            Route::put('update/{id}', 'Admins\Retail\RetailAdminUserManagementController@user_update')->name('update');
            Route::get('name', 'Admins\Retail\RetailAdminUserManagementController@user_name')->name('name');
            Route::get('edit/name/{id}', 'Admins\Retail\RetailAdminUserManagementController@user_edit_name')->name('edit.name');
        });

        Route::prefix('accounts')->name('accounts.')->group(function () {
            Route::get('', 'Admins\Retail\RetailAdminAccounts@index')->name('index');
            Route::get('/list', 'Admins\Retail\RetailAdminAccounts@list')->name('list');
            Route::post('/slip', 'Admins\Retail\RetailAdminAccounts@retail_slip')->name('retail_slip');
            Route::post('/bank_info', 'Admins\Retail\RetailAdminAccounts@retail_bank_info')->name('bank_info');
            Route::post('/bank_info_update', 'Admins\Retail\RetailAdminAccounts@retail_bank_info_update')->name('bank_info_update');
        });
    });
    Route::prefix('human_resource')->name('human_resource.')->group(function () {

        Route::get('all_user', 'Admins\AdminHumanResourseController@allusers')->name('allusers');
        Route::get('all_user_ajax', 'Admins\AdminHumanResourseController@all_user_ajax')->name('all_user_ajax');
        Route::get('download_docs', 'Admins\AdminHumanResourseController@download_docs')->name('download_docs');

        Route::prefix('employee_directory')->name('employee_directory.')->group(function () {
            Route::post('rejoin', 'Admins\AdminHumanResourseController@rejoin_employee')->name('rejoin');
            Route::post('pin', 'Admins\AdminHumanResourseController@employee_directory_pin')->name('pin');
            Route::get('', 'Admins\AdminHumanResourseController@employee_directory_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@employee_directory_list')->name('list');
            Route::post('approve', 'Admins\AdminHumanResourseController@employee_directory_approve')->name('approve');
            Route::post('required_info', 'Admins\AdminHumanResourseController@employee_directory_required_info')->name('required_info');
            Route::post('approve_individual', 'Admins\AdminHumanResourseController@employee_directory_approve_individual')->name('approve_individual');
            Route::post('reject', 'Admins\AdminHumanResourseController@employee_directory_reject')->name('reject');
            Route::get('{employee}/edit', 'Admins\AdminHumanResourseController@employee_directory_edit')->name('edit');
            Route::post('get_designation', 'Admins\AdminHumanResourseController@employee_get_designation')->name('get.designation');
            Route::post('get_cities', 'Admins\AdminHumanResourseController@employee_get_cities')->name('get.cities');
            Route::post('get_routes', 'Admins\AdminHumanResourseController@employee_get_routes')->name('get.routes');
            Route::post('{employee}/profile', 'Admins\AdminHumanResourseController@employee_directory_profile_update')->name('profile.update');
            Route::post('{employee}/medical', 'Admins\AdminHumanResourseController@employee_directory_medical_update')->name('medical.update');
            Route::post('{employee}/bank', 'Admins\AdminHumanResourseController@employee_directory_bank_update')->name('bank.update');
            Route::post('{employee}/reference', 'Admins\AdminHumanResourseController@employee_directory_reference_update')->name('reference.update');
            Route::post('{employee}/education', 'Admins\AdminHumanResourseController@employee_directory_education_update')->name('education.update');
            Route::post('{employee}/employment', 'Admins\AdminHumanResourseController@employee_directory_employment_update')->name('employment.update');
            Route::post('{employee}/attachments', 'Admins\AdminHumanResourseController@employee_directory_attachments_update')->name('attachments.update');
            Route::post('designation_logs', 'Admins\AdminHumanResourseController@designation_change_logs')->name('designation_logs');
            Route::prefix('staff')->name('staff.')->group(function () {
                Route::post('activate', 'Admins\AdminHumanResourseController@employee_directory_make_staff_activate')->name('activate');
                Route::post('deactivate', 'Admins\AdminHumanResourseController@employee_directory_make_staff_deactivate')->name('deactivate');
                Route::post('convert-to-staff', 'Admins\AdminHumanResourseController@convert_intern_to_staff')->name('convert');
            });
            Route::prefix('rider')->name('rider.')->group(function () {
                Route::post('incentive', 'Admins\AdminHumanResourseController@employee_directory_make_rider_incentive')->name('incentive');
                Route::post('permanent', 'Admins\AdminHumanResourseController@employee_directory_make_rider_permanent')->name('permanent');
                Route::post('blacklist', 'Admins\AdminHumanResourseController@employee_directory_make_rider_blacklist')->name('blacklist');
                Route::post('activate', 'Admins\AdminHumanResourseController@employee_directory_make_rider_activate')->name('activate');
                Route::post('deactivate', 'Admins\AdminHumanResourseController@employee_directory_make_rider_deactivate')->name('deactivate');
                Route::post('update', 'Admins\AdminHumanResourseController@employee_directory_make_rider_update')->name('update');
                Route::post('convert-to-staff', 'Admins\AdminHumanResourseController@convert_rider_to_staff')->name('convert');
            });

        });

        Route::prefix('reporting_location')->name('reporting_location.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@reporting_location_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@reporting_location_list')->name('list');
            Route::post('status', 'Admins\AdminHumanResourseController@reporting_location_status')->name('status');
            Route::post('add_location', 'Admins\AdminHumanResourseController@reporting_location_add')->name('add_location');
            Route::post('edit_location', 'Admins\AdminHumanResourseController@reporting_location_edit')->name('edit_location');
        });

        Route::prefix('employee_shifts')->name('employee_shifts.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@employee_shift_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@employee_shift_list')->name('list');
            Route::post('status', 'Admins\AdminHumanResourseController@employee_shift_status')->name('status');
            Route::post('add_shift', 'Admins\AdminHumanResourseController@employee_shift_add')->name('add_shift');
            Route::post('edit_shift', 'Admins\AdminHumanResourseController@employee_shift_edit')->name('edit_shift');
        });

        Route::prefix('designation')->name('designation.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@designation_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@designation_list')->name('list');
            Route::post('status', 'Admins\AdminHumanResourseController@designation_status')->name('status');
            Route::post('roles', 'Admins\AdminHumanResourseController@designation_roles')->name('roles');
            Route::post('add', 'Admins\AdminHumanResourseController@designation_add')->name('add');
            Route::post('edit', 'Admins\AdminHumanResourseController@designation_edit')->name('edit');
        });
        Route::prefix('department')->name('department.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@department_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@department_list')->name('list');
            Route::post('add', 'Admins\AdminHumanResourseController@department_add')->name('add');
            Route::post('edit', 'Admins\AdminHumanResourseController@department_edit')->name('edit');
        });

        Route::prefix('leave')->name('leave.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@leave_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@leave_list')->name('list');
            Route::post('edit', 'Admins\AdminHumanResourseController@leave_edit')->name('edit');
            Route::post('approve', 'Admins\AdminHumanResourseController@leave_approve')->name('approve');
            Route::post('reject', 'Admins\AdminHumanResourseController@leave_reject')->name('reject');
        });

        Route::prefix('rider_incentive')->name('rider_incentive.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@rider_incentive_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@rider_incentive_list')->name('list');
        });

        Route::prefix('erf')->name('erf.')->group(function () {
            Route::get('', 'Admins\AdminERFController@index')->name('index');
            Route::get('list', 'Admins\AdminERFController@list')->name('list');
            Route::get('add', 'Admins\AdminERFController@add')->name('add');
            Route::post('submit', 'Admins\AdminERFController@submit_form')->name('submit');
            Route::post('print', 'Admins\AdminERFController@print')->name('print');
            Route::post('file_upload', 'Admins\AdminERFController@file_upload')->name('file_upload');
            Route::post('reject_reason', 'Admins\AdminERFController@reject_reason')->name('reject_reason');
            Route::post('approve', 'Admins\AdminERFController@approve')->name('approve');
           // Route::get('{id}/documents','Admins\AdminERFController@documents')->name('documents');
            Route::post('documents','Admins\AdminERFController@documents')->name('documents');
            Route::post('/employee_data', 'Admins\AdminERFController@employee_data')->name('employee_data');
            Route::post('/employee_details', 'Admins\AdminERFController@employee_details')->name('employee_details');



        });

        Route::prefix('fnf')->name('fnf.')->group(function () {
            Route::get('', 'Admins\AdminFnfController@index')->name('index');
            Route::get('list', 'Admins\AdminFnfController@list')->name('list');
            Route::get('/add', 'Admins\AdminFnfController@add')->name('add');
            Route::post('/submit', 'Admins\AdminFnfController@submit')->name('submit');
            Route::post('/employee_data', 'Admins\AdminFnfController@employee_data')->name('employee_data');
            Route::get('{id}/rm', 'Admins\AdminFnfController@reporting_manager_index')->name('rm.index');
            Route::post('rm/submit', 'Admins\AdminFnfController@reporting_manager_submit')->name('rm.submit');
            Route::get('{id}/cs', 'Admins\AdminFnfController@cs_index')->name('cs.index');
            Route::post('cs/submit', 'Admins\AdminFnfController@cs_submit')->name('cs.submit');
            Route::get('{id}/administration', 'Admins\AdminFnfController@administration_index')->name('administration.index');
            Route::post('administration/submit', 'Admins\AdminFnfController@administration_submit')->name('administration.submit');
            Route::get('{id}/it_support', 'Admins\AdminFnfController@it_support_index')->name('it_support.index');
            Route::post('it_support/submit', 'Admins\AdminFnfController@it_support_submit')->name('it_support.submit');
            Route::get('{id}/finance', 'Admins\AdminFnfController@finance_index')->name('finance.index');
            Route::post('finance/submit', 'Admins\AdminFnfController@finance_submit')->name('finance.submit');
            Route::get('{id}/hr', 'Admins\AdminFnfController@hr_index')->name('hr.index');
            Route::post('hr_print', 'Admins\AdminFnfController@hr_print')->name('hr.print');
            Route::post('hr/submit', 'Admins\AdminFnfController@hr_submit')->name('hr.submit');
            Route::post('rm_status_edit', 'Admins\AdminFnfController@rm_status_edit')->name('rm_status_edit');
            Route::post('cs_status_edit', 'Admins\AdminFnfController@cs_status_edit')->name('cs_status_edit');
            Route::post('administration_status_edit', 'Admins\AdminFnfController@administration_status_edit')->name('administration_status_edit');
            Route::post('it_support_status_edit', 'Admins\AdminFnfController@it_support_status_edit')->name('it_support_status_edit');
            Route::post('finance_status_edit', 'Admins\AdminFnfController@finance_status_edit')->name('finance_status_edit');
            Route::get('{id}/hod_approval', 'Admins\AdminFnfController@hod_approval_index')->name('hod_approval_index');
            Route::post('hod_approval/submit', 'Admins\AdminFnfController@hod_approval_submit')->name('hod_approval_submit');
            Route::post('hr_status_edit', 'Admins\AdminFnfController@hr_status_edit')->name('hr_status_edit');
            Route::get('{id}/edit', 'Admins\AdminFnfController@edit_fnf_request')->name('edit_fnf_request');
            Route::post('{/update', 'Admins\AdminFnfController@update_fnf_request')->name('update_fnf_request');
            Route::get('{id}/history', 'Admins\AdminFnfController@fnf_history_index')->name('fnf_history_index');
            Route::get('{id}/history/list', 'Admins\AdminFnfController@status_history_list')->name('status_history_list');
        });

        Route::prefix('payslip')->name('payslip.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@payslip_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@payslip_list')->name('list');
            Route::post('excel', 'Admins\AdminHumanResourseController@payslip_excel_upload')->name('excel');
            Route::post('generate_payslip', 'Admins\AdminHumanResourseController@payslip_print')->name('print');
        });
    });

    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('', 'Admins\Attendance\AdminAttendanceController@admin_attendance_index')->name('index');
        Route::get('list', 'Admins\Attendance\AdminAttendanceController@admin_attendance_list')->name('list');
        Route::post('excel', 'Admins\Attendance\AdminAttendanceController@attendance_excel_upload')->name('excel');
        Route::post('print', 'Admins\Attendance\AdminAttendanceController@attendance_print')->name('print');
        Route::get('/mark', 'Admins\Attendance\AdminAttendanceController@mark_attendance_index')->name('mark');
        Route::post('/mark/submit', 'Admins\Attendance\AdminAttendanceController@mark_attendance_submit')->name('mark.submit');
        Route::get('/mark/list', 'Admins\Attendance\AdminAttendanceController@mark_attendance_list')->name('mark.list');
        Route::prefix('horizontal')->name('horizontal.')->group(function () {
            Route::get('', 'Admins\Attendance\AdminAttendanceController@admin_attendance_horizontal_index')->name('index');
            Route::post('table', 'Admins\Attendance\AdminAttendanceController@admin_attendance_horizontal_table')->name('table');
            Route::post('list', 'Admins\Attendance\AdminAttendanceController@admin_attendance_horizontal_list')->name('list');
        });
    });

    Route::prefix('rider_delivery_note_otp')->name('rider_delivery_note_otp.')->group(function () {
        Route::get('', 'Admins\UserManagementController@rider_delivery_note_otp_index')->name('index');
        Route::get('list', 'Admins\UserManagementController@rider_delivery_note_otp_list')->name('list');
    });

    Route::prefix('admin_otp')->name('admin_otp.')->group(function () {
        Route::get('', 'Admins\UserManagementController@admin_otp_index')->name('index');
        Route::get('list', 'Admins\UserManagementController@admin_otp_list')->name('list');
    });
    Route::prefix('incidence_monitoring')->name('incidence_monitoring.')->group(function (){
            Route::get('/','Admins\IncidenceMonitoringController@index')->name('index');
            Route::get('/list','Admins\IncidenceMonitoringController@list')->name('list');
            Route::post('/add','Admins\IncidenceMonitoringController@add')->name('add');
            Route::post('/get_managers','Admins\IncidenceMonitoringController@get_managers')->name('get_managers');
            Route::get('/view/{id}','Admins\IncidenceMonitoringController@view_report')->name('view_report');
            Route::prefix('comment')->name('comment.')->group(function (){
                Route::post('/add','Admins\IncidenceMonitoringController@add_comment')->name('add');
                Route::post('/get','Admins\IncidenceMonitoringController@get_comments')->name('get');
            });
            Route::post('/image_details','Admins\IncidenceMonitoringController@image_details')->name('image_details');
            Route::post('/image_submit','Admins\IncidenceMonitoringController@image_submit')->name('image_submit');
            Route::post('/update_status','Admins\IncidenceMonitoringController@update_status')->name('update_status');
            Route::get('{id}/edit/form', 'Admins\IncidenceMonitoringController@edit')->name('edit');
            Route::put('{id}/update', 'Admins\IncidenceMonitoringController@update')->name('update');

    });

    Route::prefix('qa_evaluation')->name('qa_evaluation.')->group(function (){
        Route::get('/add','Admins\QAEvaluationController@add')->name('add');
        Route::post('/handlings','Admins\QAEvaluationController@handlings')->name('handlings');
        Route::post('/submit','Admins\QAEvaluationController@submit')->name('submit');
        Route::get('','Admins\QAEvaluationController@index')->name('index');
        Route::get('list','Admins\QAEvaluationController@list')->name('list');
        Route::get('edit/{id}','Admins\QAEvaluationController@edit')->name('edit');
        Route::get('view/{id}','Admins\QAEvaluationController@view')->name('view');
        Route::get('edit_activities','Admins\QAEvaluationController@edit_activities')->name('edit_activities');
        Route::post('/handlings_edit','Admins\QAEvaluationController@handlings_edit')->name('handlings_edit');
        Route::post('/update','Admins\QAEvaluationController@update')->name('update');
        Route::post('/actvities_data','Admins\QAEvaluationController@actvities_data')->name('actvities_data');
        Route::post('update_activities','Admins\QAEvaluationController@update_activities')->name('update_activities');
    });
    Route::prefix('qa')->name('qa.')->group(function (){
        Route::prefix('high_alert')->name('high_alert.')->group(function (){
            Route::prefix('shippers')->name('shippers.')->group(function (){
                Route::get('/','Admins\HighAlertShipperController@index')->name('index');
                Route::get('/list','Admins\HighAlertShipperController@list')->name('list');
                Route::post('/add','Admins\HighAlertShipperController@add')->name('add');
                Route::post('/info','Admins\HighAlertShipperController@info')->name('info');
                Route::post('/edit','Admins\HighAlertShipperController@edit')->name('edit');
                Route::post('/remove','Admins\HighAlertShipperController@remove')->name('remove');

            });
        });
    });
});

Route::prefix('retail')->name('retail.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('retail.login');
    });
    Route::get('404', 'Auth\RetailLoginController@not_found')->name('404');

    Route::get('/login', 'Auth\RetailLoginController@showLoginForm')->name('login');
    Route::post('/login', 'Auth\RetailLoginController@login')->name('login.submit');
    Route::post('/radius', 'Auth\RetailLoginController@radius_check')->name('login.radius');
    Route::post('/verify_otp', 'Auth\RetailLoginController@verify_otp')->name('login.verify_otp');
    Route::post('/logout','Auth\RetailLoginController@logout')->name('logout');
    Route::get('/dashboard', 'Retail\RetailDashboardController@dashboard')->name('dashboard.index');

    Route::prefix('shipment')->name('shipment.')->group(function () {
        Route::prefix('book')->name('book.')->group(function () {
            Route::get('', 'Retail\RetailShipmentBookController@index')->name('index');
            Route::post('/store', 'Retail\RetailShipmentBookController@store')->name('store');
            Route::post('/slip', 'Retail\RetailShipmentBookController@slip')->name('slip');
            Route::post('/calculate_rates', 'Retail\RetailShipmentBookController@calculate_rates')->name('calculate_rates');
            Route::post('print_air_waybill', 'Retail\RetailShipmentBookController@print_air_waybill')->name('print_air_waybill');
            Route::get('/excel', 'Retail\RetailShipmentBookController@excel_index')->name('excel');
            Route::post('/excel_store', 'Retail\RetailShipmentBookController@excel_store')->name('excel_store');
        });
        Route::post('/shipper_info', 'Retail\RetailShipmentBookController@shipper_info')->name('shipper_info');
        Route::prefix('tracking_slip')->name('tracking_slip.')->group(function () {
            Route::get('', 'Retail\RetailShipmentBookController@tracking_slip_index')->name('index');
            Route::get('/list', 'Retail\RetailShipmentBookController@tracking_slip_list')->name('list');
            Route::post('/upload', 'Retail\RetailShipmentBookController@tracking_slip_upload')->name('upload');
        });
        Route::prefix('other_booking')->name('other_booking.')->group(function () {
            Route::get('', 'Retail\RetailShipmentBookController@other_booking_index')->name('index');
            Route::get('/list', 'Retail\RetailShipmentBookController@other_booking_list')->name('list');
        });
    });
    Route::prefix('cash_deposit')->name('cash_deposit.')->group(function () {
        Route::get('', 'Retail\RetailCashDepositController@index')->name('index');
        Route::get('/list', 'Retail\RetailCashDepositController@list')->name('list');
        Route::post('/shipments', 'Retail\RetailCashDepositController@shipments')->name('shipments');
        Route::post('print','Retail\RetailCashDepositController@print')->name('print');
        Route::post('finalize_rncc','Retail\RetailCashDepositController@finalize_rncc')->name('finalize_rncc');
        
    });

    Route::prefix('parcel_receiving')->name('parcel_receiving.')->group(function () {
        Route::get('', 'Retail\RetailParcelReceivingController@index')->name('index');
        Route::get('/list', 'Retail\RetailParcelReceivingController@list')->name('list');
        Route::post('/generate', 'Retail\RetailParcelReceivingController@generate')->name('generate');
        Route::post('/shipments', 'Retail\RetailParcelReceivingController@shipments')->name('shipments');
        Route::post('print','Retail\RetailParcelReceivingController@print')->name('print');
        
        Route::get('other_parcel', 'Retail\RetailParcelReceivingController@other_parcel')->name('other_parcel');
        Route::get('other_index', 'Retail\RetailParcelReceivingController@other_index')->name('other_index');
        Route::post('/other_shipment_details', 'Retail\RetailParcelReceivingController@other_shipment_details')->name('other_shipment_details');
        Route::post('/other_parcel_shipments', 'Retail\RetailParcelReceivingController@other_parcel_shipments')->name('other_parcel_shipments');
        Route::get('/other_list', 'Retail\RetailParcelReceivingController@other_list')->name('other_list');
        Route::post('/other_generate', 'Retail\RetailParcelReceivingController@other_generate')->name('other_generate');
        Route::post('/other_shipments', 'Retail\RetailParcelReceivingController@other_shipments')->name('other_shipments');
        Route::post('other_print','Retail\RetailParcelReceivingController@other_print')->name('other_print');
        
        
    });

    Route::prefix('tracking')->name('tracking.')->group(function () {
        Route::get('{tracking_number?}', 'Retail\RetailTrackingController@index')->name('index');
        Route::post('track', 'Retail\RetailTrackingController@track')->name('track');
        Route::post('track_v2', 'Retail\RetailTrackingController@track_v2')->name('track_v2');
//        Route::post('rider_information', 'Retail\RetailTrackingController@rider_information')->name('rider_information');
    });

    Route::prefix('crm')->name('crm.')->group(function () {
        Route::prefix('request')->name('request.')->group(function () {
            Route::post('add', 'Retail\RetailCRMController@add_request')->name('add');
            Route::get('{id}', 'Retail\RetailCRMController@request_details')->name('details');
        });
        Route::prefix('feedback')->name('feedback.')->group(function(){
            Route::post('add', 'Retail\RetailCRMController@add_feedback')->name('add');
        });

    });

    Route::prefix('finance')->name('finance.')->group(function () {
        Route::prefix('done_payments')->name('done_payments.')->group(function () {
            Route::post('details_print', 'Retail\RetailFinanceController@retail_done_payments_details_print')->name('print');
            Route::post('details', 'Retail\RetailFinanceController@retail_done_payments_details')->name('details');
        });
    });
    Route::prefix('cancel_shipments')->name('cancel_shipments.')->group(function(){
        Route::get('','Retail\RetailCancelShipmentsController@add_index')->name('index');
        Route::post('shipment_info', 'Retail\RetailCancelShipmentsController@get_shipment_info')->name('shipment_info');
        Route::post('store','Retail\RetailCancelShipmentsController@cancelled_shipments_store')->name('store');
    });

});

