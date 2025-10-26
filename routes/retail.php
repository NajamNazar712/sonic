<?php

Route::prefix('retail')->name('retail.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('retail.login');
    });
    Route::get('404', 'Auth\RetailLoginController@not_found')->name('404');

    Route::get('/login', 'Auth\RetailLoginController@showLoginForm')->name('login');
    Route::post('/login', 'Auth\RetailLoginController@login')->middleware('login.check')->name('login.submit');
    Route::post('/radius', 'Auth\RetailLoginController@radius_check')->name('login.radius');
    Route::post('/verify_otp', 'Auth\RetailLoginController@verify_otp')->name('login.verify_otp');
    Route::post('/logout', 'Auth\RetailLoginController@logout')->name('logout');
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
            Route::post('/add_city_req', 'Retail\RetailShipmentBookController@add_city_req')->name('add_city_req');
            Route::post('/consignee_info', 'Retail\RetailShipmentBookController@consignee_info')->name('consignee_info');
            Route::post('/get_products', 'Retail\RetailShipmentBookController@get_products')->name('get_products');
            Route::get('address_verify', 'Retail\RetailShipmentBookController@address_verify')->name('address_verify');
            Route::get('discount_code_verify/{discount_code?}', 'Retail\RetailShipmentBookController@is_discount_available_to_apply')->name('discount_code_verify');
            Route::post('previous_names_verify', 'Retail\RetailShipmentBookController@previous_names_verify')->name('previous_names_verify');
            // address_verify


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


    Route::prefix('retail_commission')->name('retail_commission.')->group(function () {
        Route::get('', 'Retail\RetailShipmentBookController@retail_commission_index')->name('index');
        Route::get('/list', 'Retail\RetailShipmentBookController@retail_commission_list')->name('list');

        Route::post('user_commission_invoice_print', 'Retail\RetailShipmentBookController@user_commission_invoice_print')->name('user_commission_invoice_print');
        Route::post('franchise_commission_invoice_print', 'Retail\RetailShipmentBookController@franchise_commission_invoice_print')->name('franchise_commission_invoice_print');
    });

    Route::prefix('cash_deposit')->name('cash_deposit.')->group(function () {
        Route::get('', 'Retail\RetailCashDepositController@index')->name('index');
        Route::get('/list', 'Retail\RetailCashDepositController@list')->name('list');
        Route::post('/shipments', 'Retail\RetailCashDepositController@shipments')->name('shipments');
        Route::post('print', 'Retail\RetailCashDepositController@print')->name('print');
        Route::post('finalize_rncc', 'Retail\RetailCashDepositController@finalize_rncc')->name('finalize_rncc');
        Route::post('hbl_konnect_cash', 'Retail\RetailCashDepositController@hbl_konnect_cash')->name('hbl_konnect_cash');
    });

    Route::prefix('parcel_receiving')->name('parcel_receiving.')->group(function () {
        Route::get('', 'Retail\RetailParcelReceivingController@index')->name('index');
        Route::get('/list', 'Retail\RetailParcelReceivingController@list')->name('list');
        Route::post('/generate', 'Retail\RetailParcelReceivingController@generate')->name('generate');
        Route::post('/shipments', 'Retail\RetailParcelReceivingController@shipments')->name('shipments');
        Route::post('print', 'Retail\RetailParcelReceivingController@print')->name('print');

        Route::get('other_parcel', 'Retail\RetailParcelReceivingController@other_parcel')->name('other_parcel');
        Route::get('other_index', 'Retail\RetailParcelReceivingController@other_index')->name('other_index');
        Route::post('/other_shipment_details', 'Retail\RetailParcelReceivingController@other_shipment_details')->name('other_shipment_details');
        Route::post('/other_parcel_shipments', 'Retail\RetailParcelReceivingController@other_parcel_shipments')->name('other_parcel_shipments');
        Route::get('/other_list', 'Retail\RetailParcelReceivingController@other_list')->name('other_list');
        Route::post('/other_generate', 'Retail\RetailParcelReceivingController@other_generate')->name('other_generate');
        Route::post('/other_shipments', 'Retail\RetailParcelReceivingController@other_shipments')->name('other_shipments');
        Route::post('other_print', 'Retail\RetailParcelReceivingController@other_print')->name('other_print');
    });

    Route::prefix('tracking')->name('tracking.')->group(function () {
        Route::get('{tracking_number?}', 'Retail\RetailTrackingController@index')->name('index');
        Route::post('track', 'Retail\RetailTrackingController@track')->name('track');
        Route::post('track_v2', 'Retail\RetailTrackingController@track_v2')->name('track_v2');
        Route::post('rider_information', 'Retail\RetailTrackingController@rider_information')->name('rider_information');
        Route::post('deliveryNotePrint', 'Retail\RetailTrackingController@deliveryNotePrint')->name('print');
    });

    Route::prefix('crm')->name('crm.')->group(function () {
        Route::prefix('request')->name('request.')->group(function () {
            Route::post('add', 'Retail\RetailCRMController@add_request')->name('add');
            Route::get('{id}', 'Retail\RetailCRMController@request_details')->name('details');
        });
        Route::prefix('feedback')->name('feedback.')->group(function () {
            Route::post('add', 'Retail\RetailCRMController@add_feedback')->name('add');
        });
    });

    Route::prefix('finance')->name('finance.')->group(function () {
        Route::prefix('done_payments')->name('done_payments.')->group(function () {
            Route::post('details_print', 'Retail\RetailFinanceController@retail_done_payments_details_print')->name('print');
            Route::post('details', 'Retail\RetailFinanceController@retail_done_payments_details')->name('details');
        });
    });
    Route::prefix('cancel_shipments')->name('cancel_shipments.')->group(function () {
        Route::get('', 'Retail\RetailCancelShipmentsController@add_index')->name('index');
        Route::post('shipment_info', 'Retail\RetailCancelShipmentsController@get_shipment_info')->name('shipment_info');
        Route::post('store', 'Retail\RetailCancelShipmentsController@cancelled_shipments_store')->name('store');
    });
    Route::prefix('return')->name('return.')->group(function () {
        Route::get('confirmation_pending', 'Retail\RetailReturnController@confirmation_pending')->name('confirmation_pending.index');
        Route::get('confirmation_pending_list', 'Retail\RetailReturnController@confirmation_pending_list')->name('confirmation_pending.list');
        Route::get('reattempt_history', 'Retail\RetailReturnController@reattempt_history')->name('reattempt_history.index');
        Route::get('reattempt_history_list', 'Retail\RetailReturnController@reattempt_history_list')->name('reattempt_history.list');
        Route::post('pending_reattempt_nsa', 'Retail\RetailReturnController@pending_reattempt_nsa')->name('pending_reattempt_nsa');
        Route::post('mark_reattempt', 'Retail\RetailReturnController@mark_reattempt')->name('mark_reattempt');

    });

    Route::prefix('arrival_service')->name('arrival_service.')->group(function () {
        Route::prefix('service')->name('service.')->group(function () {
            Route::get('', 'Retail\RetailArrivalServiceController@arrival_service_index')->name('index');
            Route::post('shipment_details', 'Retail\RetailArrivalServiceController@arrival_service_details')->name('shipment_details');
            Route::post('store', 'Retail\RetailArrivalServiceController@service_arrival_submit')->name('store');
            Route::prefix('piece')->name('piece.')->group(function () {
                Route::post('piece_details', 'Retail\RetailArrivalServiceController@arrival_piece_details')->name('piece_details');
                Route::post('shipment_details', 'Retail\RetailArrivalServiceController@arrival_piece_shipment_details')->name('shipment_details');
            });

            Route::prefix('try_and_buy')->name('try_and_buy.')->group(function (){
                Route::post('shipment_details', 'Retail\RetailArrivalServiceController@arrival_try_and_buy_shipment_details')->name('shipment_details');
            });
        });
    });

    Route::prefix('receive')->name('receive.')->group(function () {
        Route::prefix('shipment')->name('shipment.')->group(function () {
            Route::get('', 'Retail\RetailArrivalServiceController@receive_shipment_index')->name('index');
            Route::post('shipment_details', 'Retail\RetailArrivalServiceController@receive_shipment_details')->name('shipment_details');
            Route::post('store', 'Retail\RetailArrivalServiceController@receive_shipment_submit')->name('store');
            Route::prefix('piece')->name('piece.')->group(function () {
                Route::post('piece_details', 'Retail\RetailArrivalServiceController@receive_piece_details')->name('piece_details');
                Route::post('shipment_details', 'Retail\RetailArrivalServiceController@receive_piece_shipment_details')->name('shipment_details');
            });

            Route::prefix('try_and_buy')->name('try_and_buy.')->group(function (){
                Route::post('shipment_details', 'Retail\RetailArrivalServiceController@receive_try_and_buy_shipment_details')->name('shipment_details');
            });
        });
    });

    Route::prefix('last_mile')->name('last_mile.')->group(function (){
        Route::prefix('pending_shipment')->name('pending_shipment.')->group(function () {
            Route::get('', 'Retail\RetailLastMileController@index')->name('index');
            Route::get('list', 'Retail\RetailLastMileController@pending_list')->name('list');

        });
        Route::prefix('shipment')->name('shipment.')->group(function () {
            Route::post('delivered', 'Retail\RetailLastMileController@shipment_delivered')->name('delivered');
            Route::post('return', 'Retail\RetailLastMileController@return_shipment')->name('return');
        });
    });
});