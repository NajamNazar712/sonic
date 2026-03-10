<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can changepickupstatus web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Artisan;

Route::get('test-fcm-token', function () {
    return getFcmAccessToken();
});
// Route::get('/test-fcm-file', function () {
//     $path = config('services.fcm.service_account');
//     return [
//         'path' => $path,
//         'exists' => file_exists($path)
//     ];
// });
Route::get('payment_details/{id}/{id1}', 'TrackingController@payment_details')->name('payment_details');

Route::get('/', function () {
    return redirect()->route('cod.login');
});
Route::get('test-email-check/{payment_id?}', function ($payment_id = null) {
    Artisan::call('test:email_check', [
        'payment_id' => $payment_id
    ]);

    return "Command executed for payment_id: $payment_id";
});
Route::prefix('survey_form')->name('survey.')->group(function () {
    Route::get('/{id}', 'Survey\DisabledAccountIntimationSurveyController@survey')->name('index')->where(['id' => '[0-9]+']);
    Route::post('submit/email', 'Survey\DisabledAccountIntimationSurveyController@feedback_store')->name('feedback.submit');
    Route::get('submit/email/{ids}', 'Survey\DisabledAccountIntimationSurveyController@feedback_index')->name('feedback.index');
    Route::post('submit', 'Survey\DisabledAccountIntimationSurveyController@submit_survey')->name('submit');
});

Route::get('payment_details/{id}/{id1}', 'TrackingController@payment_details')->name('payment_details');

Route::get('trax_pk_validation/{company_name}/{email_address}/{phone_number}', 'APIController@trax_pk_validation')->name('trax_pk_validation');
Route::get('wp_custom_select_dropdown_data', 'APIController@wp_custom_select_dropdown_data')->name('wp_custom_select_dropdown_data');


Auth::routes();

Route::prefix('tracking')->name('tracking.')->group(function () {
    Route::get('{tracking_number?}', 'TrackingController@index')->name('index');
    Route::post('track', 'TrackingController@track')->name('track');
    Route::post('add', 'TrackingController@add_request')->name('add');
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
    Route::post('get/agreement', 'Shippers\ShipperDashboardController@get_agreement')->name('get_agreement');
    Route::post('rate/daily_visit', 'Shippers\ShipperDashboardController@rate_daily_visit')->name('rate_daily_visit');

    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
    
    //Wordpress Register Via Leads (Trax.pk)
    Route::get('/wp_register/{id}/{token}', 'Auth\LoginController@showLeadWordPressLoginForm')->name('signup');
    Route::get('/wp_register/wordpress', 'Shippers\ShipperDashboardController@wordpressLeadRegistration')->name('wordpress.register');
    Route::post('/wp_register/wordpress/salesPerson', 'Shippers\ShipperDashboardController@sales_person')->name('wordpress.salesPerson');
    Route::post('/wp_register/wordpress/get_sub_segment', 'Shippers\ShipperDashboardController@get_sub_segment')->name('wordpress.get_sub_segment');
    Route::get('/address1', 'Shippers\ShipperDashboardController@wordpressAddressView')->name('wordpress.new.address');
    Route::get('/bank1', 'Shippers\ShipperDashboardController@wordpressBankView')->name('wordpress.new.bank');

    Route::post('/login', 'Auth\LoginController@login')->middleware('login.check')->name('login.submit');
    //    Route::get('/register/','Auth\GetStartedController@index')->name('register');
    //    Route::get('/get-started', 'Auth\GetStartedController@index')->name('getstarted');
    //    Route::post('/get-started','Auth\GetStartedController@getstarted_submit')->name('getstarted');
    Route::get('/get-started-success', 'Auth\GetStartedController@getstarted_success')->name('getstarted.success');
    Route::get('/register/{lead_id?}', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('/register', 'Auth\RegisterController@register')->name('register.submit');
    Route::get('/new/address', 'Auth\RegisterController@addressView')->name('new.address');
    Route::get('/new/bank', 'Auth\RegisterController@bankView')->name('new.bank');

 


    Route::get('/email/verified/{id?}', 'Auth\RegisterController@email_verified')->name('email.verified');
    Route::post('/salesPerson', 'Auth\RegisterController@sales_person')->name('salesPerson');
    Route::post('/territory', 'Auth\RegisterController@territory')->name('territory');
    Route::post('/area', 'Auth\RegisterController@area')->name('area');
    Route::post('update/agreement_status', 'Shippers\ShipperDashboardController@agreement_status')->name('update.agreement_status');

    Route::get('access_denied', 'Shippers\ShipperDashboardController@access_denied')->name('access_denied');
    Route::get('access_denied', 'Shippers\ShipperDashboardController@wordpress_access_denied')->name('wordpress_access_denied');

    Route::get('ledger', 'Shippers\ShipperDashboardController@ledger_index')->name('ledger');
    Route::get('ledger/list', 'Shippers\ShipperDashboardController@ledger_list')->name('ledger.list');

    Route::get('/welcome', 'Shippers\ShipperDashboardController@welcome_index')->name('welcome');

    Route::get('/documents', 'Shippers\ShipperDashboardController@documents_index')->name('documents');
    Route::get('/{id}/{check}/{pdf}/documents', 'Shippers\ShipperDashboardController@viewUserDocuments')->name('documents.view');
    Route::post('/documents/upload', 'Shippers\ShipperDashboardController@uploadDocuments')->name('documents.upload');
    Route::post('/documents/confirm', 'Shippers\ShipperDashboardController@userDocumentsConfirm')->name('documents.confirm');
    Route::post('/documents/edit', 'Shippers\ShipperDashboardController@userDocumentsEdit')->name('documents.edit');

    Route::get('/welcome/list', 'Shippers\ShipperDashboardController@welcome_list')->name('welcome.list');
    Route::post('/welcome/data', 'Shippers\ShipperDashboardController@welcome_data')->name('welcome.data');
    Route::post('otp_verify', 'Shippers\ShipperDashboardController@opt_verify')->name('opt_verify');
    Route::get('opt_verify_close', 'Shippers\ShipperDashboardController@opt_verify_close')->name('opt_verify_close');
    Route::get('/dashboard', 'Shippers\ShipperDashboardController@orders_index')->name('dashboard');
    Route::get('/order/pending', 'Shippers\ShipperDashboardController@orderPending');
    Route::get('/sar_report', 'Shippers\ShipperDashboardController@sarReport')->name('sar_report');

    Route::post('/crf/update', 'Shippers\ShipperDashboardController@updateCrfSign')->name('updateSignOffCrf');;

    Route::post('get_sub_segment', 'Auth\RegisterController@get_sub_segment')->name('get_sub_segment');
    Route::get('get_products', 'Auth\RegisterController@get_products')->name('get_products');

    Route::get('referral', 'Auth\RegisterController@referral_valid')->name('referral.valid');
    Route::get('check_email', 'Auth\RegisterController@check_email')->name('register.check_email');

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('', 'Shippers\ShipperDashboardController@orders_index')->name('index');
        Route::get('list', 'Shippers\ShipperDashboardController@orders_list')->name('list');
        Route::post('search', 'Shippers\ShipperDashboardController@statistics_search')->name('search');
        Route::post('cancel', 'Shippers\ShipperDashboardController@order_cancel')->name('cancel');
        Route::post('cancel_all', 'Shippers\ShipperDashboardController@order_cancel_all')->name('cancel_all');
        Route::post('shipment_charges', 'Shippers\ShipperDashboardController@get_shipment_charges')->name('charges');
        Route::prefix('consolidate')->name('consolidate.')->group(function () {
            Route::post('shipment_info', 'Shippers\ShipperConsolidatedController@consolidate_shipment_info')->name('shipment_info');
            Route::post('submit', 'Shippers\ShipperConsolidatedController@consolidate_shipment_submit')->name('submit');
        });
    });
    //    Route::prefix('mentor_health')->name('mentor_health.')->group(function () {
    //        Route::get('', 'Shippers\ShipperDashboardController@mentor_health_index')->name('index');
    //        Route::post('add_request', 'Shippers\ShipperDashboardController@mentor_health_add_request')->name('add_request');
    //    });
    Route::prefix('shipment')->name('shipment.')->group(function () {
        Route::middleware(['PauseShipperBooking'])->prefix('book')->name('book.')->group(function () {
            Route::get('index', 'Shippers\ShipperShipmentBookController@corporate_index')->name('corporate.index');
            Route::get('address_verify', 'Shippers\ShipperShipmentBookController@address_verify')->name('address_verify');
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
            Route::get('check_negative_payable', 'Shippers\ShipperShipmentBookController@check_negative_payable')->name('check_negative_payable');


            Route::prefix('excel')->name('excel_')->group(function () {
                Route::get('', 'Shippers\ShipperShipmentBookController@excel_index')->name('index');
                Route::post('', 'Shippers\ShipperShipmentBookController@excel_store')->name('store');
            });
            Route::prefix('corporate_excel')->name('corporate_excel_')->group(function () {
                Route::get('', 'Shippers\ShipperShipmentBookController@corporate_excel_index')->name('index');
                Route::post('', 'Shippers\ShipperShipmentBookController@corporate_excel_store')->name('store');

                Route::get('/mms', 'Shippers\ShipperShipmentBookController@corporate_excel_mms_index')->name('mms');
                Route::post('/mms', 'Shippers\ShipperShipmentBookController@corporate_excel_mms_store')->name('mms.store');

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

        Route::middleware(['PauseShipperBooking'])->resource('book', 'Shippers\ShipperShipmentBookController');

        Route::middleware(['PauseShipperBooking'])->prefix('receiving_sheet')->name('receiving_sheet.')->group(function () {
            Route::get('list', 'Shippers\ShipperReceivingSheetController@list')->name('list');
            Route::get('receiving_sheet_list', 'Shippers\ShipperReceivingSheetController@receiving_sheet_list')->name('receiving_sheet_list');
            Route::get('all', 'Shippers\ShipperReceivingSheetController@all')->name('all');
            Route::put('add', 'Shippers\ShipperReceivingSheetController@add')->name('add');
            Route::put('void', 'Shippers\ShipperReceivingSheetController@void')->name('void');
            Route::post('print', 'Shippers\ShipperReceivingSheetController@print')->name('print');
            Route::get('new', 'Shippers\ShipperReceivingSheetController@create_view')->name('new');
            Route::post('info', 'Shippers\ShipperReceivingSheetController@get_shipment_details')->name('info');
            Route::post('print_receiving_sheet_and_air_waybill', 'Shippers\ShipperReceivingSheetController@print_receiving_sheet_and_air_waybill')->name('print_receiving_sheet_and_air_waybill');
            Route::post('cn/info', 'Shippers\ShipperReceivingSheetController@update_cn_info')->name('cn.info');
            Route::post('cn/update', 'Shippers\ShipperReceivingSheetController@update_consignee_info_and_special_instructions')->name('cn.update');

            Route::prefix('shipments')->name('shipments.')->group(function () {
                Route::get('', 'Shippers\ShipperReceivingSheetController@shipments_index')->name('index');
                Route::get('list', 'Shippers\ShipperReceivingSheetController@shipments_list')->name('list');
            });

            Route::prefix('excel')->name('excel.')->group(function () {
                Route::get('', 'Shippers\ShipperReceivingSheetController@shipments_excel_index')->name('index');
                Route::post('store', 'Shippers\ShipperReceivingSheetController@shipments_excel_store')->name('store');
            });
        });

        Route::middleware(['PauseShipperBooking'])->resource('receiving_sheet', 'Shippers\ShipperReceivingSheetController');

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

        Route::prefix('origin')->name('origin.')->group(function () {
            Route::get('', 'Shippers\ShipmentOriginChangeController@shipments_origin_index')->name('index');
            Route::post('store', 'Shippers\ShipmentOriginChangeController@shipments_origin_store')->name('store');
        });

        Route::prefix('return_address_change')->name('return_address_change.')->group(function () {
            Route::get('', 'Shippers\ShipmentReturnAddressController@return_address_change_excel_index')->name('index');
            Route::post('', 'Shippers\ShipmentReturnAddressController@return_address_change_excel_store')->name('store');
        });

        Route::prefix('telenor')->name('telenor.')->group(function () {
            Route::prefix('other_courier')->name('other_courier.')->group(function () {
                Route::get('', 'Shippers\TelenorOtherCourierController@index')->name('index');
                Route::post('store', 'Shippers\TelenorOtherCourierController@store')->name('store');
            });
        });
    });

    Route::prefix('dispute')->name('dispute.')->group(function () {
        Route::get('', 'Shippers\ShipperDisputeController@dispute_index')->name('index');
        Route::get('list', 'Shippers\ShipperDisputeController@dispute_list')->name('list');
        Route::post('create', 'Shippers\ShipperDisputeController@dispute_create')->name('create');
        Route::post('get/shipments', 'Shippers\ShipperDisputeController@get_shipments')->name('get.shipments');
        Route::post('get/comments', 'Shippers\ShipperDisputeController@get_comments')->name('get.comments');
        Route::post('data', 'Shippers\ShipperDisputeController@get_data')->name('data');
        Route::prefix('rebook')->name('rebook.')->group(function () {
            Route::get('', 'Shippers\ShipperDisputeController@rebook_index')->name('index');
            Route::get('list', 'Shippers\ShipperDisputeController@rebook_list')->name('list');
            Route::post('shipment/info', 'Shippers\ShipperDisputeController@get_shipment_info')->name('shipment.info');
            Route::post('shipment/update', 'Shippers\ShipperDisputeController@rebook_shipment_update')->name('shipment.update');
        });
    });
    Route::prefix('tracking')->name('tracking.')->group(function () {
        Route::get('{tracking_number?}', 'Shippers\ShipperTrackingController@index')->name('index');
        Route::post('track', 'Shippers\ShipperTrackingController@track')->name('track');
        Route::post('call_status_history', 'Shippers\ShipperTrackingController@call_status_history')->name('call_status_history');

        Route::post('case_nature_remarks', 'Shippers\ShipperTrackingController@case_nature_remarks')->name('case_nature_remarks');
        Route::post('case_nature_service_remarks', 'Shippers\ShipperTrackingController@case_nature_service_remarks')->name('case_nature_service_remarks');
        Route::post('case_nature_claim_remarks', 'Shippers\ShipperTrackingController@case_nature_claim_remarks')->name('case_nature_claim_remarks');

        Route::post('shipper_visibility', 'Shippers\ShipperTrackingController@shipper_visibility')->name('shipper_visibility');
        Route::post('rider_information', 'Shippers\ShipperTrackingController@rider_information')->name('rider_information');
        Route::post('get_shipment_geo_codes', 'Shippers\ShipperTrackingController@get_shipment_geo_codes')->name('get_shipment_geo_codes');
        Route::post('update_geo_codes', 'Shippers\ShipperTrackingController@update_geo_codes')->name('update_geo_codes');

    });
    Route::prefix('order')->name('order.')->group(function () {
        Route::get('{order_id?}', 'Shippers\ShipperTrackingController@order_index')->name('index');
        Route::post('order_track', 'Shippers\ShipperTrackingController@order_track')->name('track');
    });
    Route::prefix('packaging')->name('packaging.')->group(function () {
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('', 'Shippers\ShipperPackagingMaterialController@packaging_request')->name('index');
            Route::get('list', 'Shippers\ShipperPackagingMaterialController@packaging_request_list')->name('list');
            Route::post('details', 'Shippers\ShipperPackagingMaterialController@packaging_request_details')->name('details');
            Route::post('sizes', 'Shippers\ShipperPackagingMaterialController@packaging_request_sizes')->name('sizes');
            Route::post('submit', 'Shippers\ShipperPackagingMaterialController@packaging_request_submit')->name('submit');
            Route::post('cancel', 'Shippers\ShipperPackagingMaterialController@packaging_request_cancel')->name('cancel');

            Route::get('categories', 'Shippers\ShipperPackagingMaterialController@select_categories')->name('categories');
            Route::get('category/{id}', 'Shippers\ShipperPackagingMaterialController@category_products')->name('category');
            Route::get('product/{id}', 'Shippers\ShipperPackagingMaterialController@product_details')->name('product');
            Route::post('get_charges', 'Shippers\ShipperPackagingMaterialController@get_charges')->name('get_charges');
            Route::post('add_to_cart', 'Shippers\ShipperPackagingMaterialController@add_to_cart')->name('add_to_cart');
            Route::post('get_cart_count', 'Shippers\ShipperPackagingMaterialController@cart_count')->name('get_cart_count');
            Route::get('checkout', 'Shippers\ShipperPackagingMaterialController@checkout')->name('checkout');
            Route::post('remove_product', 'Shippers\ShipperPackagingMaterialController@remove_product')->name('remove_product');

            Route::prefix('cart')->name('cart.')->group(function () {
                Route::get('', 'Shippers\ShipperPackagingMaterialController@packaging_request_cart_index')->name('index');
                Route::post('details', 'Shippers\ShipperPackagingMaterialController@packaging_request_cart_details')->name('details');
            });
        });
    });
    Route::prefix('return')->name('return.')->group(function () {
        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('', 'Shippers\ShipperReturnController@confirmation_pending_index')->name('index');
            Route::get('list', 'Shippers\ShipperReturnController@confirmation_pending_list')->name('list');
            Route::post('marked/status', 'Shippers\ShipperReturnController@return_marked_status')->name('marked.status');
            Route::post('marked/status/single', 'Shippers\ShipperReturnController@return_marked_single_status')->name('marked.status.single');
            Route::post('reattempt/status', 'Shippers\ShipperReturnController@return_reattempt_status')->name('reattempt.status');
            Route::post('reattempt/nsa', 'Shippers\ShipperReturnController@return_reattempt_nsa')->name('reattempt.nsa');
            Route::post('reattempt/status/single', 'Shippers\ShipperReturnController@return_reattempt_single_status')->name('reattempt.status.single');
            Route::post('marked/self_collection', 'Shippers\ShipperReturnController@change_status_to_self_collection')->name('marked.self_collection');
            Route::post('consignee', 'Shippers\ShipperReturnController@blacklist_search_consignee')->name('consignee');
            Route::post('get_manual_shipment_geo_codes', 'Shippers\ShipperReturnController@get_manual_shipment_geo_codes')->name('get_manual_shipment_geo_codes');
            Route::post('update_manual_geo_codes', 'Shippers\ShipperReturnController@update_manual_geo_codes')->name('update_manual_geo_codes');
        });
        Route::prefix('reattempt_history')->name('reattempt_history.')->group(function () {
            Route::get('', 'Shippers\ShipperReturnController@return_reattempt_history_index')->name('index');
            Route::get('list', 'Shippers\ShipperReturnController@return_reattempt_history_list')->name('list');
        });
        Route::prefix('confirmed')->name('confirmed.')->group(function () {
            Route::get('', 'Shippers\ShipperReturnController@return_confirmed_index')->name('index');
            Route::get('list', 'Shippers\ShipperReturnController@return_list')->name('list');
        });
        Route::prefix('sheet')->name('sheet.')->group(function () {
            Route::prefix('pending')->name('pending.')->group(function () {
                Route::get('', 'Shippers\ShipperReturnController@return_sheet_pending_index')->name('index');
                Route::get('list', 'Shippers\ShipperReturnController@return_sheet_pending_list')->name('list');
            });
            Route::prefix('receive')->name('receive.')->group(function () {
                Route::get('', 'Shippers\ShipperReturnController@return_sheet_receive_index')->name('index');
                Route::post('shipment_info', 'Shippers\ShipperReturnController@return_sheet_receive_shipment_info')->name('shipment_info');
                Route::post('submit', 'Shippers\ShipperReturnController@return_sheet_receive_submit')->name('submit');
            });
            Route::prefix('history')->name('history.')->group(function () {
                Route::get('', 'Shippers\ShipperReturnController@return_sheet_history_index')->name('index');
                Route::get('list', 'Shippers\ShipperReturnController@return_sheet_history_list')->name('list');
            });
        });
    });

    Route::prefix('substitute_account_management')->name('substitute_account_management.')->group(function () {
        Route::get('', 'Shippers\ShipperSubstituteAccountManagementController@index')->name('index');
        Route::get('list', 'Shippers\ShipperSubstituteAccountManagementController@list')->name('list');
        Route::get('email', 'Shippers\ShipperSubstituteAccountManagementController@email')->name('email');
        Route::post('status', 'Shippers\ShipperSubstituteAccountManagementController@status')->name('status');

        Route::prefix('add')->name('add.')->group(function () {
            Route::get('', 'Shippers\ShipperSubstituteAccountManagementController@add_index')->name('index');
            Route::post('', 'Shippers\ShipperSubstituteAccountManagementController@add_store')->name('store');
        });

        Route::prefix('update/{id}')->name('update.')->group(function () {
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
            Route::post('arrival_shipments', 'Shippers\ShipperFinanceController@payments_arrival_shipments')->name('arrival_shipments');
            Route::post('fintech_shipments', 'Shippers\ShipperFinanceController@payments_fintech_shipments')->name('fintech_shipments');
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

        Route::prefix('shipment_ledger')->name('shipment_ledger.')->group(function () {
            Route::get('', 'Shippers\ShipperFinanceController@shipment_ledger')->name('index');
            Route::get('list', 'Shippers\ShipperFinanceController@shipment_ledger_list')->name('list');
        });
    });

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::prefix('qsr')->name('qsr.')->group(function () {
            Route::get('', 'Shippers\ShipperReportsController@qsr_index')->name('index');
            Route::get('list', 'Shippers\ShipperReportsController@qsr_list')->name('list');
        });
        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('', 'Shippers\ShipperReportsController@sales_index')->name('index');
            Route::post('list', 'Shippers\ShipperReportsController@sales_list')->name('list');

            Route::prefix('telenor')->name('telenor.')->group(function () {
                Route::get('', 'Shippers\ShipperReportsController@sales_telenor_index')->name('index');
                Route::get('list', 'Shippers\ShipperReportsController@sales_telenor_list')->name('list');
            });
        });
        Route::prefix('summary')->name('summary.')->group(function () {
            Route::get('', 'Shippers\ShipperReportsController@summary_index')->name('index');
            Route::get('list', 'Shippers\ShipperReportsController@summary_list')->name('list');
            Route::post('data', 'Shippers\ShipperReportsController@summary_data')->name('data');
        });

        Route::prefix('adjustments')->name('adjustments.')->group(function () {
            Route::get('', 'Shippers\ShipperReportsController@adjustments_index')->name('index');
            Route::get('list', 'Shippers\ShipperReportsController@adjustments_list')->name('list');
        });

        Route::prefix('weight_reconciliation')->name('weight_reconciliation.')->group(function () {
            Route::get('', 'Shippers\ShipperReportsController@weight_reconciliation_index')->name('index');
            Route::post('list', 'Shippers\ShipperReportsController@weight_reconciliation_list')->name('list');
        });



        Route::prefix('delivery_and_return')->name('delivery_and_return.')->group(function () {
            Route::get('', 'Shippers\ShipperReportsController@delivery_and_return_index')->name('index');
        });
        Route::prefix('confirmation_pending_report')->name('confirmation_pending_report.')->group(function () {
            Route::get('', 'Shippers\ShipperReportsController@confirmation_shipments_index')->name('index');
            Route::get('list', 'Shippers\ShipperReportsController@confirmation_shipments_list')->name('list');
        });
        Route::prefix('daraz_mis')->name('daraz_mis.')->group(function () {
            Route::get('', 'Shippers\ShipperReportsController@daraz_mis_index')->name('index');
            Route::get('list', 'Shippers\ShipperReportsController@daraz_mis_list')->name('list');
        });
        Route::prefix('mms')->name('mms.')->group(function () {
            Route::get('', 'Shippers\ShipperReportsController@mms_index')->name('index');
            Route::post('list', 'Shippers\ShipperReportsController@mms_list')->name('list');
        });
        Route::prefix('special_dashboard')->name('special_dashboard.')->group(function () {
            Route::get('mms', 'Shippers\ShipperReportsController@special_dashboard_mms_index')->name('index');
            Route::post('list', 'Shippers\ShipperReportsController@special_dashboard_mms_list')->name('list');
        });


        Route::prefix('rider_pickup')->name('rider_pickup.')->group(function () {
            Route::get('', 'Shippers\ShipperReportsController@rider_pickup_index')->name('index');
            Route::get('list', 'Shippers\ShipperReportsController@rider_pickup_list')->name('list');
            Route::post('scanned_shipments', 'Shippers\ShipperReportsController@rider_pickup_scanned_shipments')->name('scanned_shipments');
            Route::post('arrived_shipments', 'Shippers\ShipperReportsController@rider_pickup_arrived_shipments')->name('arrived_shipments');
            Route::post('without_scan_shipments', 'Shippers\ShipperReportsController@rider_pickup_without_scan_shipments')->name('without_scan_shipments');
        });
        Route::prefix('project_arrival')->name('project_arrival.')->group(function () {
            Route::get('', 'Shippers\ShipperReportsController@project_arrival_index')->name('index');
            Route::get('list', 'Shippers\ShipperReportsController@project_arrival_list')->name('list');
        });
    });

    Route::prefix('rates')->name('rates.')->group(function () {
        Route::prefix('view')->name('view.')->group(function () {
            Route::get('', 'Shippers\ShipperDashboardController@view_rates_index')->name('index');
        });
    });

    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('/register/user/success', 'Auth\RegisterController@register_success');
    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('/register/name/match/{name}', 'Auth\RegisterController@checkCompanyName');
    Route::get('/register/email/match/{email}/{id}', 'Auth\RegisterController@checkCompanyEmail')->name('check.email');
    Route::get('/register/name/match/{name}/{id}', 'Auth\RegisterController@checkCompanyNameProfile')->name('check.name');
    Route::get('terms_and_conditions/{token}/{id}/accept', 'ShipperAgreementController@accept')->name('terms.accept');
    Route::get('terms_and_conditions/{token}/{id}/download', 'ShipperAgreementController@crf_download')->name('terms.download');
    Route::get('/terms/success', 'Auth\RegisterController@register_success')->name('terms.success');

    //user profile
    Route::post('add_shipper_id', 'Shippers\ShipperDashboardController@storeShipperId')->name('add.shipper_id');
    Route::get('/profile', 'Shippers\ShipperDashboardController@userProfile')->name('edit.profile');
    Route::post('updateprofile', 'Shippers\ShipperDashboardController@updateProfile')->name('update.profile');
    Route::post('SignInWalletUser', 'Shippers\ShipperDashboardController@SignInWalletUser')->name('SignInWalletUser');
    Route::post('updateprofilewalletbulk', 'Shippers\ShipperDashboardController@updateprofilewalletbulk')->name('update.bulk.profile_wallet');
    Route::post('update/profile/password', 'Shippers\ShipperDashboardController@update_profile_password')->name('update.profile.password');
    Route::get('getpickups', 'Shippers\ShipperDashboardController@getPickups')->name('get.pickups');
    Route::get('getbanks', 'Shippers\ShipperDashboardController@getBanks')->name('get.banks');
    Route::post('default_bank', 'Shippers\ShipperDashboardController@updateDefaultBanks')->name('default.bank');
    Route::post('add_bank', 'Shippers\ShipperDashboardController@addBank')->name('add.bank');
    Route::post('verify_pincode', 'Shippers\ShipperDashboardController@verifyPincode')->name('verify.pin.code');
    Route::post('changepickupstatus', 'Shippers\ShipperDashboardController@pickupStatusChange')->name('change.pickup.status');
    Route::post('addpickup', 'Shippers\ShipperDashboardController@addPickup')->name('add.pickup');
    Route::post('editpickup', 'Shippers\ShipperDashboardController@editPickup')->name('edit.pickup');
    Route::post('updateprofile', 'Shippers\ShipperDashboardController@updateProfile')->name('update.profile');
    Route::post('edit/emails', 'Shippers\ShipperDashboardController@edit_notification_emails')->name('edit.emails');
    Route::post('add/emails', 'Shippers\ShipperDashboardController@add_notification_emails')->name('add.emails');
    Route::get('contacts', 'Shippers\ShipperDashboardController@contacts')->name('contacts');
    Route::post('update_invoice_sort', 'Shippers\ShipperDashboardController@update_invoice_sort')->name('update_invoice_sort');
    Route::get('/phone_unique', 'Shippers\ShipperDashboardController@shipper_phone_unique')->name('profile.shipper_phone_unique');

    Route::prefix('resources')->name('resources.')->group(function () {
        Route::get('', 'Shippers\ShipperResourcesController@index')->name('index');
        Route::get('city_list', 'Shippers\ShipperResourcesController@get_network_list')->name('city_list');
    });

    Route::prefix('crm')->name('crm.')->group(function () {
        Route::prefix('request')->name('request.')->group(function () {
            Route::get('', 'Shippers\ShipperCRMController@index')->name('index');
            Route::get('list', 'Shippers\ShipperCRMController@requests_list')->name('list');
            Route::get('{id}/details', 'Shippers\ShipperCRMController@request_details')->name('details');
            Route::post('add', 'Shippers\ShipperCRMController@add_request')->name('add');
            Route::post('re_open', 'Shippers\ShipperCRMController@re_open_request')->name('re_open');
            Route::post('/lost/claim', 'Shippers\ShipperCRMController@lost_claim')->name('lost.claim');
            Route::post('feedback', 'Shippers\ShipperCRMController@customer_feedback')->name('feedback');
            Route::post('card_data', 'Shippers\ShipperCRMController@card_data')->name('card_data');
        });
        Route::prefix('feedback')->name('feedback.')->group(function () {
            Route::post('add', 'Shippers\ShipperCRMController@add_feedback')->name('add');
        });
        Route::prefix('comment')->name('comment.')->group(function () {
            Route::post('add', 'Shippers\ShipperCRMController@add_comment')->name('add');
            Route::post('get', 'Shippers\ShipperCRMController@get_latest_comment')->name('get');
        });

        Route::prefix('bulk_claim')->name('bulk_claim.')->group(function () {
            Route::get('', 'Shippers\ShipperCRMController@bulk_claim_index')->name('index');
            Route::post('store', 'Shippers\ShipperCRMController@bulk_claim_submit')->name('submit');
            Route::post('shipment_details', 'Shippers\ShipperCRMController@bulk_claim_shipment_details')->name('shipment_details');
        });
    });

    Route::prefix('cancelled_shipments')->name('cancelled_shipments.')->group(function () {
        Route::get('', 'Shippers\ShipperShipmentCancelController@index')->name('index');
        Route::get('list', 'Shippers\ShipperShipmentCancelController@list')->name('list');
        Route::put('bulk_revert', 'Shippers\ShipperShipmentCancelController@bulk_revert')->name('bulk_revert');
        Route::put('revert', 'Shippers\ShipperShipmentCancelController@revert')->name('revert');
    });

    Route::prefix('cancelled_shipments_arrival')->name('cancelled_shipments_arrival.')->group(function () {
        Route::get('', 'Shippers\ShipperShipmentCancelController@cancelled_shipments_arrival_index')->name('cancelled_shipments_arrival_index');
        Route::post('cancelled_shipments_arrival', 'Shippers\ShipperShipmentCancelController@cancelled_shipments_arrival')->name('cancelled_shipments_arrival');
    });

    Route::prefix('intercept')->name('intercept.')->group(function () {
        Route::get('/{row_id}', 'Shippers\ShipperInterceptReBookController@intercept_re_book_index')->name('index');
        Route::post('update', 'Shippers\ShipperInterceptReBookController@intercept_re_book_update')->name('update');
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
            Route::get('bank_info', 'Shippers\ShipperGlobalSettingsController@shipping_information_bank_info')->name('bank_info');
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

    Route::prefix('multiple_pieces')->name('multiple_pieces.')->group(function () {
        Route::get('', 'Shippers\ShipperShipmentPieceController@hold_index')->name('index');
        Route::get('list', 'Shippers\ShipperShipmentPieceController@hold_list')->name('list');
        Route::post('single_piece', 'Shippers\ShipperShipmentPieceController@single_piece')->name('single_piece');
        Route::post('wait_remaining_pieces', 'Shippers\ShipperShipmentPieceController@wait_remaining_pieces')->name('wait_remaining_pieces');
        Route::post('return_back_to_shipper', 'Shippers\ShipperShipmentPieceController@return_back_to_shipper')->name('return_back_to_shipper');
        Route::prefix('resolved')->name('resolved.')->group(function () {
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

    Route::prefix('quick_search')->name('quick_search.')->group(function () {
        Route::get('', 'Shippers\ShipperDashboardController@quick_search_index')->name('index');
        Route::get('list', 'Shippers\ShipperDashboardController@quick_search_list')->name('list');
    });

    Route::prefix('nps')->name('nps.')->group(function () {
        Route::post('nps_survey_check', 'Shippers\NpsSurveyShipperController@nps_survey_check')->name('nps_survey_check');
        Route::post('ratting_submit', 'Shippers\NpsSurveyShipperController@ratting_submit')->name('ratting_submit');
        Route::post('nps_skip', 'Shippers\NpsSurveyShipperController@nps_skip')->name('nps_skip');
    });

    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('login', 'FingaIntegrationController@login')->name('login');
        Route::get('finja_dashboard', 'FingaIntegrationController@finja_dashboard')->name('finja_dashboard');
        Route::get('on_boarding', 'FingaIntegrationController@on_boarding')->name('on_boarding');
        Route::get('signup', 'FingaIntegrationController@signup')->name('signup');
        Route::get('wallet_user', 'FingaIntegrationController@wallet_user')->name('users');
    });
});

Route::prefix('agent')->name('agent.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('agent.login');
    });

    Route::get('/login', 'Auth\AgentLoginController@showLoginForm')->name('login');
    Route::post('/login', 'Auth\AgentLoginController@login')->name('login.submit');
    Route::get('/logout', 'Auth\AgentLoginController@logout')->name('logout');
    Route::post('/logout', 'Auth\AgentLoginController@logout')->name('logout');
    Route::post('/credentials', 'Auth\AgentLoginController@credentials')->name('login.credentials');
    Route::post('/verify_otp', 'Auth\AgentLoginController@verify_otp')->name('login.verify_otp');


    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('', 'Agent\ReturnV2Controller@index')->name('index');
        Route::post('get_shipment_reason', 'Agent\ReturnV2Controller@get_shipment_reason')->name('get_shipment_reason');
        Route::post('get_ticket', 'Agent\ReturnV2Controller@get_ticket')->name('get_ticket');
        Route::get('intercepted_shipment', 'Agent\ReturnV2Controller@get_intercepted_shipment')->name('get_intercepted_shipment');
        Route::post('submit', 'Agent\ReturnV2Controller@submit_ticket')->name('submit_ticket');
    });
    Route::post('save_coordinates', 'Admins\AdminController@save_coordinates')->name('save_coordinates');
});

//For Admin Routes
require __DIR__ . '/admin.php';

//For Retail Routes
require __DIR__ . '/retail.php';
