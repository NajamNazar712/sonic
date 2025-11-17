<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


    Route::name('api.')->group(function () {
    Route::get('fetch_complaints', 'APIController@fetch_complaints')->name('fetch_complaints');
    Route::post('shipment/track/public/crm/request', 'APIController@add_request')->name('crm.track.public');
    Route::post('login', 'APIController@login')->name('login');
    Route::post('user_login', 'APIController@bolt_login')->name('user_login');
    Route::post('forget_pin', 'APIController@bolt_forget_pin')->name('forget_pin');
    Route::post('reset_pin', 'APIController@bolt_reset_pin')->name('reset_pin');
    Route::post('store_device_token', 'APIController@store_device_token')->name('store_device_token');
    Route::post('delete_device_token', 'APIController@delete_device_token')->name('delete_device_token');
    Route::post('rcp_sms_from_consignee', 'APIController@rcp_sms_from_consignee')->name('rcp_sms_from_consignee');
    Route::post('fintech_getToken','APIController@fintech_getToken')->name('fintech_getToken');
    Route::get('marco_cities', 'APIController@marco_cities')->name('marco_cities');
    Route::middleware('FinvoWalletAuth')->group(function () {
        Route::post('v2/fintech_getToken','APIController@fintech_getToken')->name('v2.fintech_getToken');
    });
    Route::middleware('FinvoWalletUser')->group(function () {
        Route::post('fin_sms', 'APIController@fin_sms')->name('fin_sms');
        Route::post('fintech_charges','APIController@fintech_charges')->name('fintech_charges');
        Route::post('v2/fintech_charges/bulk','APIController@fintech_charges_bulk')->name('fintech_charges_bulk');
        Route::post('v2/fintech_charges/bulk/fix','APIController@fintech_charges_bulk_dev_fix')->name('fintech_charges_bulk_fix');
        Route::post('v2/fintech_account_type', 'APIController@fintech_account_type')->name('finance_types.update.bulk');
    });



    Route::post('employee_attendance_details', 'APIController@employee_checkin')->name('employee_attendance_details');

    Route::post('lead_website', 'APIController@storeWebsiteLead');

    Route::middleware('APIToken')->group(function () {
        Route::post('verify', 'APIController@verify')->name('verify');

        Route::get('pickup_addresses', 'APIController@pickup_addresses')->name('pickup_addresses');
        Route::post('pickup_address/add', 'APIController@pickup_address_add')->name('pickup_address.add');

        Route::prefix('shipment')->name('shipment.')->group(function () {
            Route::post('book', 'APIController@shipment_book')->name('book');
            Route::post('book/intl', 'APIController@shipment_book_international')->name('book.intl');
            Route::post('book/gul_ahmed', 'APIController@shipment_book_gul_ahmed')->name('book.gul_ahmed');
            Route::get('air_waybill', 'APIController@shipment_air_waybill')->name('air_waybill');
            Route::post('book/zellbury', 'APIController@shipment_book_zellbury')->name('book.zellbury');

            Route::prefix('status')->name('status.')->group(function () {
                Route::get('', 'APIController@shipment_status')->name('status');
                Route::get('order_id', 'APIController@shipment_status_order_id')->name('order_id');
                Route::get('consingee_phone_number', 'APIController@shipment_status_consingee_phone_number')->name('consingee_phone_number');
            });

            Route::prefix('track')->name('track.')->group(function () {
                Route::get('', 'APIController@shipment_track')->name('track');
                Route::get('order_id', 'APIController@shipment_track_order_id')->name('order_id');
            });

            Route::get('charges', 'APIController@shipment_charges')->name('charges');
            Route::get('payment_status', 'APIController@shipment_payment_status')->name('payment_status');
            Route::get('payments', 'APIController@shipment_payments')->name('payments');
            Route::post('cancel', 'APIController@shipment_cancel')->name('cancel');

            Route::post('eta', 'APIController@shipment_status_eta')->name('eta');
            Route::post('book/daraz', 'APIController@shipment_book_daraz')->name('book.daraz');
            Route::post('book/jazzcash', 'APIController@shipment_book_jazzcash')->name('book.jazzcash');
        });
        Route::prefix('request')->name('request.')->group(function () {
            Route::post('crm', 'APIController@crm_request_create')->name('crm');
            Route::post('rcp', 'APIController@rcp_request_create')->name('rcp');
        });

        Route::post('pickup_address/add', 'APIController@pickup_address_add')->name('pickup_address.add');

        Route::prefix('receiving_sheet')->name('receiving_sheet.')->group(function () {
            Route::post('create', 'APIController@receiving_sheet_create')->name('create');
            Route::get('view', 'APIController@receiving_sheet_view')->name('view');
            Route::post('add', 'APIController@receiving_sheet_add')->name('add');
            Route::post('remove', 'APIController@receiving_sheet_void')->name('remove');
            Route::post('cancel', 'APIController@receiving_sheet_cancel')->name('cancel');
            Route::post('list', 'APIController@receiving_sheet_list')->name('list');
            Route::get('print', 'APIController@receiving_sheet_print')->name('print');
        });

        Route::get('cities', 'APIController@cities')->name('cities');

        Route::post('charges_calculate', 'APIController@charges_calculate')->name('charges_calculate');
        Route::post('consolidate', 'APIController@shipment_consolidate')->name('consolidate');
        Route::prefix('return')->name('return.')->group(function () {
            Route::get('pending', 'APIController@return_confirmation_pending')->name('pending2');
            Route::post('pending', 'APIController@return_confirmation_pending_update')->name('pending');
        });


        Route::prefix('shopify')->name('shopify.')->group(function () {
            Route::get('cities', 'APIController@shopify_cities')->name('cities');
            Route::post('invoice', 'ShopifyController@invoice_settings')->name('invoice');
            Route::post('air_waybill', 'APIController@shipment_air_waybill_shopify_invoice')->name('air_waybill');
        });

        Route::get('catalyst_users', 'APIController@catalyst_users')->name('catalyst_users');

        Route::post('payments', 'APIController@payments')->name('payments');

        Route::get('ideas_payments', 'APIController@ideas_payments')->name('ideas_payments');

        Route::post('invoice', 'APIController@invoice')->name('invoice');


        Route::prefix('return')->name('return.')->group(function () {
            Route::prefix('shipments')->name('shipments.')->group(function () {
                Route::post('return_shipment_info', 'APIController@return_shipment_info')->name('return_shipment_info');
                Route::post('received_return_shipments', 'APIController@shipper_received_shipments')->name('received_return_shipments');
                Route::get('return_shipments_list', 'APIController@return_shipments_list')->name('return_shipments_list');
            });
        });
        Route::prefix('partner')->name('partner.')->group(function () {
            Route::post('mark-prepaid', 'APIController@can_specific_user_change_amount')->name('can_specific_user_change_amount');
        });



    });


    Route::middleware('APIThrottle:500,0.5')->prefix('shipment')->name('shipment.')->group(function () {
        Route::get('track/public', 'APIController@shipment_track_public')->name('track.public');
        Route::get('track/consignee/public', 'APIController@shipment_track_consignee_public')->name('track.consignee.public');
    });


    Route::prefix('fintech')->name('fintech.')->group(function () {
        Route::post('shipment-details', 'APIController@get_shipment_details')->name('shipment-details');
        Route::post('payment-details', 'APIController@fintech_payment_detials')->name('payment-details');
    });

    Route::prefix('rider')->name('rider.')->group(function () {
        //Obsoleted
        Route::post('login', 'Rider\RiderAPIController@login')->name('login');
        Route::post('login_v2', 'Rider\RiderAPIController@login_v2')->name('login_v2');
        Route::post('login_v3', 'Rider\RiderAPIController@login_v2')->name('login_v3');
        Route::post('login_v4', 'Rider\RiderAPIController@login_v4')->name('login_v4');
        Route::any('signup', 'Rider\RiderAPIController@rider_signup')->name('signup');
        Route::get('cities', 'Rider\RiderAPIController@cities')->name('cities');
        Route::get('check_pin', 'Rider\RiderAPIController@check_pin')->name('check_pin');

        //Current
        Route::post('login_v5', 'Rider\RiderAPIController@login_v4')->name('login_v5');
        Route::get('slider', 'Rider\RiderAPIController@rider_ticker_images')->name('slider');
        Route::get('shipment_settings', 'Rider\RiderAPIController@shipment_attempt_settings')->name('shipment_settings');
        Route::post('forget_pin', 'Rider\RiderAPIController@forget_pin')->name('forget_pin');
        Route::post('reset_pin', 'Rider\RiderAPIController@reset_pin')->name('reset_pin');
        Route::get('logout', 'Rider\RiderAPIController@logout')->name('logout');

        Route::prefix('register_request')->name('register_request.')->group(function () {
            //Obsoleted
            Route::post('store', 'Rider\RiderAPIController@rider_signup_v2')->name('store2');
            Route::post('store_v2', 'Rider\RiderAPIController@rider_signup_v3')->name('store_v3');
            Route::post('attachment_store', 'Rider\RiderAPIController@rider_attachments_store')->name('attachment_store');

            //Current
            Route::get('signup_data', 'Rider\RiderAPIController@signup_data')->name('signup_data');
            Route::post('validate_data', 'Rider\RiderAPIController@validate_cnic_phone_number')->name('validate_data');
            Route::prefix('store_v3')->name('store_v3.')->group(function () {
                Route::post('required_details', 'Rider\RiderAPIController@signup_required_details')->name('required_details');
                Route::post('optional_details', 'Rider\RiderAPIController@signup_optional_details')->name('optional_details');
            });
            Route::post('attachment_store_v2', 'Rider\RiderAPIController@rider_attachments_store_v2')->name('attachment_store_v2');
            Route::post('attachment_view', 'Rider\RiderAPIController@rider_attachments_view')->name('attachment_view');
            Route::post('attachment_delete', 'Rider\RiderAPIController@rider_attachments_delete')->name('attachment_delete');
            Route::post('attachment_check', 'Rider\RiderAPIController@rider_attachments_check')->name('attachment_check');
            Route::post('get_line_managers', 'Rider\RiderAPIController@get_line_managers')->name('get_line_managers');
        });

        Route::post('rider_attendance_details', 'Rider\RiderAPIController@rider_checkin')->name('rider_attendance_details');

        Route::middleware('RiderAPIToken')->group(function () {
            Route::get('check_app_version', 'Rider\RiderAPIController@check_bolt_version')->name('check_app_version');
            Route::post('validate_otp', 'Rider\RiderAPIController@validate_otp')->name('validate_otp');
            Route::prefix('pickup')->name('pickup.')->group(function () {

                //Obsoleted
                Route::get('summary', 'Rider\RiderAPIController@pickup_summary')->name('pickup_summary');
                Route::post('pick', 'Rider\RiderAPIController@pickup_pick')->name('pickup_pick');
                Route::post('not_pick', 'Rider\RiderAPIController@pickup_not_pick')->name('pickup_not_pick');
                Route::post('action_log', 'Rider\RiderAPIController@pickup_action_log')->name('pickup_action_log');
                Route::post('check_tracking_number', 'Rider\RiderAPIController@pickup_check_tracking_number')->name('check_tracking_number');
                Route::post('pick_v2', 'Rider\RiderAPIController@pickup_pick_v2')->name('pickup_pick_v2');
                Route::post('not_pick_v2', 'Rider\RiderAPIController@pickup_not_pick_v2')->name('pickup_not_pick_v2');

                //Current
                Route::get('summary_v2', 'Rider\RiderAPIController@pickup_summary_v2')->name('pickup_summary_v2');
                Route::post('action_log_v2', 'Rider\RiderAPIController@pickup_action_log_v2')->name('pickup_action_log_v2');
                Route::post('not_pick_v3', 'Rider\RiderAPIController@pickup_not_pick_v3')->name('pickup_not_pick_v3');
                Route::post('pick_v3', 'Rider\RiderAPIController@pickup_pick_v3')->name('pickup_pick_v3');
                Route::post('scan_shipment_assign', 'Rider\RiderAPIController@scan_shipment_assign')->name('scan_shipment_assign');
                Route::post('scan_shipment_detail', 'Rider\RiderAPIController@scan_shipment_detail')->name('scan_shipment_detail');
                Route::post('pickup_in_route', 'Rider\RiderAPIController@pickup_in_route')->name('pickup_in_route');
                Route::post('scan_rider_picked_shipment', 'Rider\RiderAPIController@scan_rider_picked_shipment')->name('scan_rider_picked_shipment');
            });

            //Obsoleted
            Route::prefix('location')->name('location.')->group(function () {
                Route::post('', 'Rider\RiderAPIController@get_rider_location')->name('get');
            });

            Route::prefix('rider_remarks')->name('rider_remarks.')->group(function () {
                Route::post('add', 'Rider\RiderAPIController@rider_remarks')->name('add');
                Route::get('get', 'Rider\RiderAPIController@rider_remark_list')->name('list');
            });


            //Current
            Route::post('location_v2', 'Rider\RiderAPIController@get_rider_location_v2')->name('location_v2');

            Route::prefix('delivery')->name('delivery.')->group(function () {
                //Obsoleted
                Route::get('summary', 'Rider\RiderAPIController@delivery_summary')->name('delivery_summary');
                Route::post('delivered', 'Rider\RiderAPIController@shipment_delivered')->name('delivered');
                Route::post('undelivered', 'Rider\RiderAPIController@shipment_undelivered')->name('undelivered');
                Route::get('summary/multiple', 'Rider\RiderAPIController@delivery_summary_multiple')->name('delivery_summary_multiple');
                Route::get('summary/multiple_v2', 'Rider\RiderAPIController@delivery_summary_multiple_v2')->name('delivery_summary_multiple_v2');
                Route::get('summary/multiple_v3', 'Rider\RiderAPIController@delivery_summary_multiple_v3')->name('delivery_summary_multiple_v3');
                Route::get('summary/multiple_v4', 'Rider\RiderAPIController@delivery_summary_multiple_v4')->name('delivery_summary_multiple_v4');
                Route::post('undelivered_v2', 'Rider\RiderAPIController@shipment_undelivered_v2')->name('undelivered_v2');
                Route::post('delivered_v2', 'Rider\RiderAPIController@shipment_delivered_v2')->name('delivered_v2');
                Route::post('delivered_v3', 'Rider\RiderAPIController@shipment_delivered_v3')->name('delivered_v3');
                Route::post('delivered_v4', 'Rider\RiderAPIController@shipment_delivered_v4')->name('delivered_v4');

                //Current
                Route::get('summary/multiple_v5', 'Rider\RiderAPIController@delivery_summary_multiple_v5')->name('delivery_summary_multiple_v5');
                Route::get('summary/multiple_v6', 'Rider\RiderAPIController@delivery_summary_multiple_v6')->name('delivery_summary_multiple_v6');
                Route::post('undelivered_v3', 'Rider\RiderAPIController@shipment_undelivered_v3')->name('undelivered_v3');
                Route::post('undelivered_v4', 'Rider\RiderAPIController@shipment_undelivered_v4')->name('undelivered_v4');
                Route::post('rvrsub_reason', 'Rider\RiderAPIController@shipmentUndeliveredRvrSubReason')->name('rvrsub_reason');
                Route::get('undelivered_reason_map', 'Rider\RiderAPIController@undelivered_reason_map')->name('undelivered_reason_map');

                Route::post('delivered_v5', 'Rider\RiderAPIController@shipment_delivered_v5')->name('delivered_v5');
                Route::post('delivery_in_route', 'Rider\RiderAPIController@delivery_in_route')->name('delivery_in_route');
                Route::post('action_log', 'Rider\RiderAPIController@delivery_action_log')->name('delivery_action_log');
                Route::post('otp_generate', 'Rider\RiderAPIController@generate_otp_for_consignee')->name('otp_generate');
                Route::post('pending_for_verification', 'Rider\RiderAPIController@pending_for_verification')->name('pending_for_verification');

                Route::post('print', 'Rider\RiderAPIController@delivery_print')->name('print');

            });
            Route::prefix('comments')->name('comments.')->group(function () {
                Route::post('add', 'Rider\RiderAPIController@crm_comment_add')->name('add');
            });

            Route::prefix('history')->name('history.')->group(function () {
                //Obsoleted
                Route::post('pickup', 'Rider\RiderAPIController@pickups_history')->name('pickup');
                Route::post('delivery', 'Rider\RiderAPIController@delivery_history')->name('delivery');
                Route::post('return', 'Rider\RiderAPIController@return_history')->name('return');

                //Current
                Route::post('pickup_v2', 'Rider\RiderAPIController@pickups_history_v2')->name('pickup_v2');
                Route::post('delivery_v2', 'Rider\RiderAPIController@delivery_history_v2')->name('delivery_v2');
                Route::post('delivery_v3', 'Rider\RiderAPIController@delivery_history_v3')->name('delivery_v3');
                Route::post('return_v2', 'Rider\RiderAPIController@return_history_v2')->name('return_v2');
                Route::post('history_details', 'Rider\RiderAPIController@history_details')->name('history_details');
                Route::post('history_details_v1', 'Rider\RiderAPIController@history_details_v1')->name('history_details_v1');
            });

            Route::prefix('return')->name('return.')->group(function () {
                //Obsoleted
                Route::get('summary', 'Rider\RiderAPIController@return_summary_multiple')->name('return_summary');
                Route::post('delivered', 'Rider\RiderAPIController@return_shipment_delivered')->name('delivered');
                Route::post('undelivered', 'Rider\RiderAPIController@return_shipment_undelivered')->name('undelivered');
                Route::post('undelivered_v2', 'Rider\RiderAPIController@return_shipment_undelivered_v2')->name('undelivered_v2');
                Route::post('undelivered_v3', 'Rider\RiderAPIController@return_shipment_undelivered_v3')->name('undelivered_v3');

                //Current
                Route::post('index', 'Rider\RiderAPIController@return_create_index')->name('index');
                Route::post('get_shipment_details', 'Rider\RiderAPIController@get_shipment_details')->name('get_shipment_details');
                Route::post('get_piece_details', 'Rider\RiderAPIController@get_piece_details')->name('get_piece_details');
                Route::post('create', 'Rider\RiderAPIController@return_note_create')->name('create');
                Route::get('summary_v2', 'Rider\RiderAPIController@return_summary_multiple_v2')->name('summary_v2');
                Route::post('delivered_v2', 'Rider\RiderAPIController@return_shipment_delivered_v2')->name('delivered_v2');
                Route::post('undelivered_v4', 'Rider\RiderAPIController@return_shipment_undelivered_v4')->name('undelivered_v4');
                Route::post('action_log', 'Rider\RiderAPIController@return_action_log')->name('action_log');

                Route::post('return_note/print', 'Rider\RiderAPIController@return_note_print')->name('return_note.print');

            });

            Route::get('rider_wallet', 'Rider\RiderAPIController@rider_wallet')->name('rider_wallet');
            Route::get('rider_profile', 'Rider\RiderAPIController@rider_profile')->name('rider_profile');
            Route::get('profile', 'Rider\RiderAPIController@rider_profile')->name('profile');
            Route::get('notification_history', 'Rider\RiderAPIController@notification_history')->name('notification_history');

            Route::prefix('attendance')->name('attendance.')->group(function () {
                //Obsoleted
                Route::post('month_history', 'Rider\RiderAPIController@month_attendance_history')->name('month_history');
                Route::post('detail', 'Rider\RiderAPIController@attendance_details')->name('detail');
                Route::post('mark', 'Rider\RiderAPIController@mark_attendance')->name('mark');
                Route::post('history', 'Rider\RiderAPIController@attendance_history')->name('history');
                Route::post('mark_v2', 'Rider\RiderAPIController@mark_attendance_v2')->name('mark_v2');

                //Current
                Route::get('shift', 'Rider\RiderAPIController@rider_shift')->name('shift');
                Route::post('mark_v3', 'Rider\RiderAPIController@mark_attendance_v3')->name('mark_v3');
                Route::post('detail_v2', 'Rider\RiderAPIController@attendance_details_v2')->name('detail_v2');
                Route::post('month_history_v2', 'Rider\RiderAPIController@month_attendance_history_v2')->name('month_history_v2');
            });

            Route::prefix('adjustment')->name('adjustment.')->group(function () {
                Route::post('index', 'Rider\RiderAPIController@adjustment_index')->name('index');
                Route::post('apply', 'Rider\RiderAPIController@adjustment_apply')->name('apply');
                Route::get('list', 'Rider\RiderAPIController@employee_adjustment_list')->name('list');
            });
            //Obsoleted
            Route::post('rider_incentives', 'Rider\RiderAPIController@rider_incentive')->name('rider_incentives');
            Route::post('rider_incentives_v2', 'Rider\RiderAPIController@rider_incentive_v2')->name('rider_incentives_v2');

            //Current
            Route::post('rider_incentives_v3', 'Rider\RiderAPIController@rider_incentive_v3')->name('rider_incentives_v3');
            Route::post('rider_incentives_v4', 'Rider\RiderAPIController@rider_incentive_v4')->name('rider_incentives_v4');

            Route::prefix('retail')->name('retail.')->group(function () {
                Route::get('retail_data', 'Rider\RiderAPIController@retail_index')->name('retail_data');
                Route::post('retail_bank_info', 'Rider\RiderAPIController@retail_bank_info')->name('retail_bank_info');
                Route::post('calculate_charges', 'Rider\RiderAPIController@retail_shipment_calculate_rates')->name('calculate_charges');
                Route::post('retail_shipment_store', 'Rider\RiderAPIController@retail_shipment_store')->name('retail_shipment_store');
                Route::post('retail_shipment_store_v2', 'Rider\RiderAPIController@retail_shipment_store_v2')->name('retail_shipment_store_v2');
                Route::post('retail_shipment_store_v3', 'Rider\RiderAPIController@retail_shipment_store_v3')->name('retail_shipment_store_v3');
            });

            
            Route::prefix('profile')->name('profile.')->group(function () {
                //Obsoleted
                Route::get('index', 'Rider\RiderAPIController@get_profile')->name('index');
                Route::get('check', 'Rider\RiderAPIController@check_profile')->name('check');
                Route::post('update', 'Rider\RiderAPIController@update_profile')->name('update');
                Route::get('check_v2', 'Rider\RiderAPIController@check_profile_v2')->name('check_v2');

                //Current
                Route::get('index_v2', 'Rider\RiderAPIController@get_profile_v2')->name('index_v2');
                Route::post('check_v3', 'Rider\RiderAPIController@check_profile_v3')->name('check_v3');
                Route::post('update_v2', 'Rider\RiderAPIController@update_profile_v2')->name('update_v2');
            });

            Route::post('payslip', 'Rider\RiderAPIController@rider_payslip')->name('payslip');
            Route::post('fake_status', 'Rider\RiderAPIController@fake_status_count')->name('fake_status');

            Route::prefix('leave')->name('leave.')->group(function () {
                //Obsoleted
                Route::post('index', 'Rider\RiderAPIController@leave_index')->name('index');
                Route::post('apply', 'Rider\RiderAPIController@leave_apply')->name('apply');
                Route::get('list', 'Rider\RiderAPIController@employee_leave_list')->name('list');

                //Current
                Route::post('index_v2', 'Rider\RiderAPIController@leave_index_v2')->name('index_v2');
                Route::post('apply_v2', 'Rider\RiderAPIController@leave_apply_v2')->name('apply_v2');
                Route::get('list_v2', 'Rider\RiderAPIController@employee_leave_list_v2')->name('list_v2');
                Route::post('calender', 'Rider\RiderAPIController@view_calender')->name('calender');
            });
            Route::get('employee_id', 'Rider\RiderAPIController@get_employee_id')->name('employee_id');

            Route::prefix('delivery_note')->name('delivery_note.')->group(function () {
                Route::post('index', 'Rider\RiderAPIController@delivery_note_index')->name('index');
                Route::post('shipment_details', 'Rider\RiderAPIController@get_delivery_shipment_details')->name('shipment_details');
                Route::post('piece_details', 'Rider\RiderAPIController@get_delivery_piece_details')->name('piece_details');
                Route::post('generate_otp', 'Rider\RiderAPIController@delivery_note_otp_generation')->name('generate_otp');
                Route::post('verify_otp', 'Rider\RiderAPIController@delivery_note_otp_verification')->name('verify_otp');
                Route::post('create', 'Rider\RiderAPIController@create_delivery_note')->name('create');
            });

            Route::prefix('quick_tracking')->name('quick_tracking.')->group(function () {
                Route::post('scan_shipment', 'Rider\RiderAPIController@scan_shipment')->name('index');
            });

            Route::prefix('leadmanagement')->name('leadmanagement.')->group(function () {
                Route::prefix('lead')->name('lead.')->group(function () {
                    Route::get('', 'LeadAPIController@index')->name('index');
                    Route::post('', 'LeadAPIController@store')->name('store');
                    Route::post('fetch', 'LeadAPIController@show')->name('show');
                    // Route::post('/update/{id}', 'LeadAPIController@update')->name('update');
                    Route::post('/city_territories', 'LeadAPIController@city_territories')->name('city_territories');
                    Route::post('/territory_areas/{territory_id}', 'LeadAPIController@territory_areas')->name('territory_areas');
                    Route::get('/services_list','LeadAPIController@services_list')->name('services_list');
                    Route::get('/city_list','LeadAPIController@city_list')->name('city_list');
                });
            });

            //logistic api endpoints
            Route::prefix('v1')->name('v1.')->group(function (){

                Route::prefix('logistic')->name('logistic.')->group(function (){
                    Route::get('data','Rider\Logistic\Api\RiderLogisticApiController@logistic_data')->name('data');
                    Route::post('store','Rider\Logistic\Api\RiderLogisticApiController@logistic_booking_store')->name('store');
                    Route::post('shipper','Rider\Logistic\Api\RiderLogisticApiController@get_shipper_detail')->name('shipper');
                    Route::post('store_image', 'Rider\Logistic\Api\RiderLogisticApiController@store_image')->name('store_image');
                });

            });

        });


    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::post('login', 'AdminAPIController@login')->name('login');
        Route::post('login_v2', 'AdminAPIController@login')->name('login_v2');
        Route::post('login_v3', 'AdminAPIController@login_v3')->name('login_v3');
        Route::post('login_v4', 'AdminAPIController@login_v4')->name('login_v4');
        Route::post('login_v5', 'AdminAPIController@login_v5')->name('login_v5');
        Route::get('slider', 'Rider\RiderAPIController@rider_ticker_images')->name('slider');
        Route::get('slider', 'AdminAPIController@admin_ticker_images')->name('slider');
        Route::post('forget_password', 'AdminAPIController@forget_password')->name('forget_password');
        Route::post('forget_pin', 'AdminAPIController@forget_pin')->name('forget_pin');
        Route::post('reset_pin', 'AdminAPIController@reset_pin')->name('reset_pin');
        Route::get('check_pin', 'AdminAPIController@check_pin')->name('check_pin');
        Route::get('logout', 'AdminAPIController@logout')->name('logout');
        Route::get('check_app_version', 'AdminAPIController@check_bolt_version')->name('check_app_version');
        Route::prefix('register_request')->name('register_request.')->group(function () {
            Route::get('signup_data', 'AdminAPIController@signup_data')->name('signup_data');
            Route::post('validate_data', 'AdminAPIController@validate_cnic_phone_number')->name('validate_data');
            Route::prefix('store_v3')->name('store_v3.')->group(function () {
                Route::post('required_details', 'AdminAPIController@signup_required_details')->name('required_details');
                Route::post('optional_details', 'AdminAPIController@signup_optional_details')->name('optional_details');
            });
            Route::post('store', 'AdminAPIController@admin_signup')->name('store');
            Route::post('store_v2', 'AdminAPIController@admin_signup_v2')->name('store');
            Route::post('attachment_store', 'AdminAPIController@admin_attachments_store')->name('attachment_store');
            Route::post('attachment_store_v2', 'AdminAPIController@admin_attachments_store_v2')->name('attachment_store_v2');
            Route::post('attachment_view', 'AdminAPIController@admin_attachments_view')->name('attachment_view');
            Route::post('attachment_delete', 'AdminAPIController@admin_attachments_delete')->name('attachment_delete');
            Route::post('attachment_check', 'AdminAPIController@admin_attachments_check')->name('attachment_check');
            Route::post('get_line_managers', 'AdminAPIController@get_line_managers')->name('get_line_managers');
            Route::post('get_working_shift', 'AdminAPIController@get_staff_working_shift')->name('get_working_shift');
        });

        Route::middleware('AdminAPIToken')->group(function () {
            Route::get('check_app_version', 'AdminAPIController@check_bolt_version')->name('check_app_version');
            Route::post('verify', 'AdminAPIController@verify')->name('verify');
            Route::get('check_permissions', 'AdminAPIController@check_permissions')->name('check_permissions');
            Route::post('return_note_details', 'AdminAPIController@return_note_details')->name('return_note_details');
            Route::post('history_update_image', 'AdminAPIController@history_update_image')->name('history_update_image');
            Route::post('validate_otp', 'AdminAPIController@validate_otp')->name('validate_otp');

            Route::prefix('attendance')->name('attendance.')->group(function () {
                Route::get('shift', 'AdminAPIController@employee_shift')->name('shift');
                Route::post('month_history', 'AdminAPIController@month_attendance_history')->name('month_history');
                Route::post('month_history_v2', 'AdminAPIController@month_attendance_history_v2')->name('month_history_v2');
                Route::post('detail', 'AdminAPIController@attendance_details')->name('detail');
                Route::post('mark', 'AdminAPIController@mark_attendance')->name('mark');
                Route::post('history', 'AdminAPIController@attendance_history')->name('history');
                Route::post('mark_v2', 'AdminAPIController@mark_attendance_v2')->name('mark_v2');
                Route::post('mark_v3', 'AdminAPIController@mark_attendance_v3')->name('mark_v3');
                Route::post('detail_v2', 'AdminAPIController@attendance_details_v2')->name('detail_v2');
                Route::post('mark_api', 'AdminAPIController@mark_attendance_api')->name('mark_api');
                Route::get('flutter_detail', 'AdminAPIController@flutter_attendance_details')->name('flutter_detail');
                Route::post('flutter_mark', 'AdminAPIController@flutter_mark_attendance')->name('flutter_mark');
            });

            Route::prefix('retail')->name('retail.')->group(function () {
                Route::get('retail_data', 'AdminAPIController@retail_index')->name('retail_data');
                Route::post('retail_bank_info', 'AdminAPIController@retail_bank_info')->name('retail_bank_info');
                Route::post('calculate_charges', 'AdminAPIController@retail_shipment_calculate_rates')->name('calculate_charges');
                Route::post('retail_shipment_store', 'AdminAPIController@retail_shipment_store')->name('retail_shipment_store');
                Route::post('retail_shipment_store_v2', 'AdminAPIController@retail_shipment_store_v2')->name('retail_shipment_store_v2');
                Route::post('retail_shipment_store_v3', 'AdminAPIController@retail_shipment_store_v3')->name('retail_shipment_store_v3');
                Route::post('retail_center_location', 'AdminAPIController@retail_center_location')->name('retail_center_location');
            });
            Route::prefix('track')->name('track.')->group(function () {
                Route::get('', 'AdminAPIController@shipmentTrack')->name('track');
            });
            Route::prefix('master_cargo')->name('master_cargo.')->group(function () {
                Route::post('list', 'AdminAPIController@master_cargo')->name('list');
                Route::post('bags', 'AdminAPIController@cargo_bags')->name('bags');
                Route::post('bags/validate', 'AdminAPIController@cargo_bags_validator')->name('bags.validate');
                Route::post('bags/details', 'AdminAPIController@cargo_bags_details')->name('bags.details');
                Route::post('recieve', 'AdminAPIController@cargo_bag_recieve')->name('recieve');
            });

            Route::get('notification_history', 'AdminAPIController@notification_history')->name('notification_history');
            Route::post('location_v2', 'AdminAPIController@attendance_notification')->name('location_v2');
            Route::post('payslip', 'AdminAPIController@admin_payslip')->name('payslip');

            Route::prefix('leave')->name('leave.')->group(function () {
                Route::post('index', 'AdminAPIController@leave_index')->name('index');
                Route::post('apply', 'AdminAPIController@leave_apply')->name('apply');
                Route::get('list', 'AdminAPIController@employee_leave_list')->name('list');
                Route::get('approver_list', 'AdminAPIController@approver_leave_list')->name('approver_list');
                Route::post('approve', 'AdminAPIController@leave_approve')->name('approve');
                Route::post('reject', 'AdminAPIController@leave_reject')->name('reject');
                Route::post('detail', 'AdminAPIController@leave_detail')->name('detail');
                Route::post('calender', 'AdminAPIController@view_calender')->name('calender');
                Route::post('hr_edit', 'AdminAPIController@hr_leave_edit')->name('hr_edit');

                //revamp
                Route::post('index_v2', 'AdminAPIController@leave_index_v2')->name('index_v2');
                Route::post('apply_v2', 'AdminAPIController@leave_apply_v2')->name('apply_v2');
                Route::get('list_v2', 'AdminAPIController@employee_leave_list_v2')->name('list_v2');
                Route::get('approver_list_v2', 'AdminAPIController@approver_leave_list_v2')->name('approver_list_v2');
                Route::post('approve_v2', 'AdminAPIController@leave_approve_v2')->name('approve_v2');
                Route::post('hod_approve', 'AdminAPIController@hod_approve')->name('hod_approve');
                Route::post('reject_v2', 'AdminAPIController@leave_reject_v2')->name('reject_v2');
            });

            Route::prefix('adjustment')->name('adjustment.')->group(function () {
                Route::post('index', 'AdminAPIController@adjustment_index')->name('index');
                Route::post('apply', 'AdminAPIController@adjustment_apply')->name('apply');
                Route::get('list', 'AdminAPIController@employee_adjustment_list')->name('list');
                Route::get('approver_list', 'AdminAPIController@approver_adjustment_list')->name('approver_list');
                Route::post('approve', 'AdminAPIController@adjustment_approve')->name('approve');
                Route::post('reject', 'AdminAPIController@adjustment_reject')->name('reject');
            });

            Route::prefix('profile')->name('profile.')->group(function () {
                Route::get('index', 'AdminAPIController@get_profile')->name('index');
                Route::get('check', 'AdminAPIController@check_profile')->name('check');
                Route::post('update', 'AdminAPIController@update_profile')->name('update');

                Route::get('index_v2', 'AdminAPIController@get_profile_v2')->name('index_v2');
                Route::get('check_v2', 'AdminAPIController@check_profile_v2')->name('check_v2');
                Route::post('check_v3', 'AdminAPIController@check_profile_v3')->name('check_v3');
                Route::post('update_v2', 'AdminAPIController@update_profile_v2')->name('update_v2');

            });
            Route::get('profile', 'AdminAPIController@admin_profile')->name('profile');
            Route::get('employee_id', 'AdminAPIController@get_employee_id')->name('employee_id');
            Route::post('trax_directory', 'AdminAPIController@trax_directory')->name('trax_directory');
            Route::post('trax_directory_v2', 'AdminAPIController@trax_directory_v2')->name('trax_directory_v2');
            Route::get('trax_directory_index', 'AdminAPIController@trax_directory_index')->name('trax_directory_index');

            Route::prefix('leads')->name('leads.')->group(function () {
                Route::post('list', 'AdminAPIController@leads_list')->name('list');
                Route::post('list_v2', 'AdminAPIController@leads_list_v2')->name('list_v2');
                Route::post('status_list', 'AdminAPIController@lead_statuses')->name('status_list');
                Route::post('status_update', 'AdminAPIController@lead_status_update')->name('status_update');
                Route::post('add_remarks', 'AdminAPIController@add_remarks')->name('add_remarks');
                Route::post('view_remarks', 'AdminAPIController@view_remarks')->name('view_remarks');
            });

            Route::prefix('pending_pick_list')->name('pick_list.')->group(function () {
                Route::get('list', 'AdminAPIController@pending_pick_list')->name('list');
                Route::post('detail', 'AdminAPIController@pick_list_details')->name('detail');
                Route::post('barcode_validate', 'AdminAPIController@pick_list_barcode_validate')->name('barcode_validate');
                Route::post('receive', 'AdminAPIController@pick_list_receive')->name('receive');
            });

            Route::prefix('daily_visit')->name('daily_visit.')->group(function () {
                Route::get('index', 'AdminAPIController@daily_visit_index')->name('index');
                Route::post('store', 'AdminAPIController@daily_visit_store')->name('store');
                Route::post('shipper_details', 'AdminAPIController@shipper_details')->name('shipper_details');
                Route::any('report', 'AdminAPIController@daily_visit_report')->name('report');
            });

            Route::prefix('return')->name('return.')->group(function () {
                Route::post('index', 'AdminAPIController@return_create_index')->name('index');
                Route::post('get_riders', 'AdminAPIController@get_riders_by_hub')->name('get_riders');
                Route::post('get_shipment_details', 'AdminAPIController@get_shipment_details')->name('get_shipment_details');
                Route::post('get_piece_details', 'AdminAPIController@get_piece_details')->name('get_piece_details');
                Route::post('create', 'AdminAPIController@return_note_create')->name('create');
                Route::get('receive_list', 'AdminAPIController@get_return_note_list')->name('receive_list');
                Route::post('shipments_list', 'AdminAPIController@return_note_shipments_list')->name('shipments_list');
                Route::post('reason', 'AdminAPIController@return_reason')->name('reason');
                Route::post('submit_individual', 'AdminAPIController@return_status_submit_individual')->name('submit_individual');
                Route::post('submit_all', 'AdminAPIController@return_status_submit_all')->name('submit_all');
                Route::post('image_upload', 'AdminAPIController@return_image_upload')->name('image_upload');
                Route::get('note_requests', 'AdminAPIController@return_note_requests')->name('note_requests');
                Route::post('reject', 'AdminAPIController@return_note_requests_reject')->name('reject');
                Route::post('approve', 'AdminAPIController@return_note_requests_approve')->name('approve');
            });

            Route::prefix('sales_target')->name('sales_target.')->group(function () {
                Route::get('list', 'AdminAPIController@sales_person_targets')->name('list');
                Route::post('history', 'AdminAPIController@sales_person_target_history')->name('history');
                Route::post('history_v1', 'AdminAPIController@sales_person_target_v1')->name('history_v1');
            });

            Route::prefix('delivery_note')->name('delivery_note.')->group(function () {
                Route::post('index', 'AdminAPIController@delivery_note_index')->name('index');
                Route::post('shipment_details', 'AdminAPIController@get_delivery_shipment_details')->name('shipment_details');
                Route::post('piece_details', 'AdminAPIController@get_delivery_piece_details')->name('piece_details');
                Route::post('generate_otp', 'AdminAPIController@delivery_note_otp_generation')->name('generate_otp');
                Route::post('verify_otp', 'AdminAPIController@delivery_note_otp_verification')->name('verify_otp');
                Route::post('create', 'AdminAPIController@create_delivery_note')->name('create');
                Route::get('note_requests', 'AdminAPIController@delivery_note_requests')->name('note_requests');
                Route::post('note_requests_v2', 'AdminAPIController@delivery_note_requests_v2')->name('note_requests');
                Route::post('reject', 'AdminAPIController@delivery_note_requests_reject')->name('reject');
                Route::post('approve', 'AdminAPIController@delivery_note_requests_approve')->name('approve');
            });

            Route::prefix('crm_request')->name('crm_request.')->group(function () {
                Route::get('index', 'AdminAPIController@crm_request_index')->name('index');
                Route::get('list', 'AdminAPIController@crm_request_list')->name('list');
                Route::post('submit', 'AdminAPIController@crm_request_submit')->name('submit');
            });

            Route::prefix('quick_tracking')->name('quick_tracking.')->group(function () {
                Route::post('scan_shipment', 'AdminAPIController@scan_shipment')->name('index');
            });


            //APIs for Bot Calling
        Route::get('bot_get_ticket/{tracking_number?}', 'Agent\BotCallingController@bot_get_ticket_details')->name('bot_get_ticket');
        Route::post('bot_submit_ticket', 'Agent\BotCallingController@bot_submit_ticket')->name('bot_submit_ticket');

        });

        Route::middleware('AdminAPIDWSToken')->group(function () {
            Route::post('dws_weight', 'AdminAPIController@dws_weight')->name('dws_weight');
            Route::post('store_dws_image', 'AdminAPIController@store_dws_image')->name('store_dws_image');
        });
    });

    Route::prefix('retail_user')->name('retail_user.')->group(function () {
        Route::post('login_v2', 'Retail\RetailAPIController@login')->name('login_v2');
        Route::post('login_v3', 'Retail\RetailAPIController@login')->name('login_v3');
        Route::post('login_v4', 'Retail\RetailAPIController@login')->name('login_v4');
        Route::post('login_v5', 'Retail\RetailAPIController@login')->name('login_v5');
        Route::get('slider', 'Retail\RetailAPIController@retail_ticker_images')->name('slider');

        Route::middleware('RetailUserAPIToken')->group(function () {
            Route::prefix('retail')->name('retail.')->group(function () {
                Route::get('retail_data', 'Retail\RetailAPIController@retail_index')->name('retail_data');
                Route::post('retail_bank_info', 'Retail\RetailAPIController@retail_bank_info')->name('retail_bank_info');
                Route::post('calculate_charges', 'Retail\RetailAPIController@retail_shipment_calculate_rates')->name('calculate_charges');
                Route::post('retail_shipment_store', 'Retail\RetailAPIController@retail_shipment_store')->name('retail_shipment_store');
                Route::post('retail_shipment_store_v2', 'Retail\RetailAPIController@retail_shipment_store_v2')->name('retail_shipment_store_v2');
                Route::post('retail_shipment_store_v3', 'Retail\RetailAPIController@retail_shipment_store_v3')->name('retail_shipment_store_v3');
            });
        });
    });

    Route::prefix('consignee')->name('consignee.')->group(function () {
        Route::post('get_info', 'ConsigneeAPIController@consignee_info')->name('get_info');
        Route::post('test', 'ConsigneeAPIController@test')->name('test');
        Route::post('consignee_otp', 'ConsigneeAPIController@consignee_otp')->name('consignee_otp');
        Route::post('consignee_forget_pin_otp', 'ConsigneeAPIController@consignee_forget_pin_otp')->name('consignee_forget_pin_otp');
        Route::post('otp_verify', 'ConsigneeAPIController@consignee_otp_verification')->name('otp_verify');
        Route::post('consignee_signup', 'ConsigneeAPIController@consignee_signup')->name('consignee_signup');
        Route::post('login', 'ConsigneeAPIController@login')->name('login');
        Route::post('shipment_history', 'ConsigneeAPIController@shipment_history')->name('shipment_history');
        Route::post('update_pin', 'ConsigneeAPIController@update_pin')->name('update_pin');
        Route::middleware('ConsigneeAPIToken')->group(function () {
            Route::post('update_profile', 'ConsigneeAPIController@update_profile')->name('update_profile');
            Route::prefix('shipments')->name('shipments.')->group(function () {
                Route::get('active', 'ConsigneeAPIController@active_shipments')->name('active');
                Route::get('previous', 'ConsigneeAPIController@previous_shipments')->name('previous');
            });
            Route::post('update_address', 'ConsigneeAPIController@address_change_request')->name('update_address');
            Route::get('notification_history', 'ConsigneeAPIController@notification_history')->name('notification_history');
        });
    });

    Route::prefix('shipper')->name('shipper.')->group(function () {
        Route::post('login', 'ShipperAPIController@login')->name('login');
        Route::post('reset_password', 'ShipperAPIController@reset_password')->name('reset_password');
        Route::post('send_otp', 'ShipperAPIController@sendOtp')->name('reset_password');
        Route::post('verify_otp', 'ShipperAPIController@verifyOtp')->name('reset_password');



        Route::post('test', 'ShipperAPIController@test')->name('test');
        Route::middleware('ShipperAPIToken')->group(function () {

            //airwaybill
            Route::post('shipment/air_waybill_pdf', 'ShipperOrderManagementApiController@shipment_air_waybill')->name('shipment.air_waybill');

            Route::prefix('retail')->name('retail.')->group(function () {
                Route::get('shipment_track', 'APIController@retail_shipment_track')->name('track');
            });

            Route::post('wallet_registration', 'ShipperAPIController@updateprofilewalletbulk')->name('wallet_registration');
            Route::post('wallet_login', 'ShipperAPIController@wallet_login')->name('wallet_login');
            Route::post('pod_tracking', 'ShipperAPIController@shipment_pod_tracking')->name('pod_tracking');
            Route::post('shipment_history', 'ShipperAPIController@shipment_history')->name('shipment_history');
            Route::prefix('subscription')->name('subscription.')->group(function () {
                Route::post('add', 'ShipperAPIController@shipper_subscription_add')->name('add');
                Route::get('list', 'ShipperAPIController@shipper_subscription_list')->name('list');
                Route::post('delete', 'ShipperAPIController@shipper_subscription_delete')->name('delete');
            });
            Route::get('get_shipper_info','ShipperAPIController@get_shipper_info')->name('get_shipper_info');
            Route::get('notification_history', 'ShipperAPIController@notification_history')->name('notification_history');
            Route::get('is_wallet_user' ,'ShipperAPIController@is_wallet_user')->name('is_wallet_user');
            
            Route::prefix('add_request')->name('add_request.')->group(function () {
                Route::get('index', 'ShipperAPIController@add_request_index')->name('index');
                Route::post('submit', 'ShipperAPIController@add_request_submit')->name('submit');
                Route::post('lost_claim', 'ShipperAPIController@lost_claim')->name('lost_claim');
            });

            Route::prefix('rcp')->name('rcp.')->group(function () {
                Route::get('list', 'ShipperAPIController@confirmation_pending_list')->name('list');
                Route::get('shipper-advice', 'ShipperAPIController@shipper_sar')->name('shipper-advice');
                Route::post('mark_return_confirm', 'ShipperAPIController@mark_return_confirm')->name('mark_return_confirm');
                Route::post('mark_reattempt', 'ShipperAPIController@mark_reattempt')->name('mark_reattempt');
                Route::prefix('intercept')->name('intercept.')->group(function () {
                    Route::post('index', 'ShipperAPIController@intercept_re_book_index')->name('index');
                    Route::post('submit', 'ShipperAPIController@intercept_re_book_submit')->name('submit');
                });
            });

            Route::get('get_booking_types', 'ShipperAPIController@booking_types')->name('get_booking_types');

            Route::prefix('corporate')->name('corporate.')->group(function () {
                Route::get('index', 'ShipperAPIController@corporate_index')->name('index');
                Route::post('ftl_info', 'ShipperAPIController@get_ftl_info')->name('ftl_info');
                Route::post('shipping_modes', 'ShipperAPIController@corporate_shipping_modes')->name('shipping_modes');
                Route::post('submit', 'APIController@shipment_book')->name('submit');
            });
            Route::prefix('reimbursement')->name('reimbursement.')->group(function () {
                Route::get('index', 'ShipperAPIController@reimbursement_index')->name('index');
                Route::post('shipping_modes', 'ShipperAPIController@reimbursement_shipping_modes')->name('shipping_modes');
                Route::post('submit', 'APIController@shipment_book')->name('submit');
            });

            Route::post('shipping_address','ShipperAPIController@shipping_address')->name('shipping_address');

            Route::get('profile', 'ShipperAppController@profile')->name('profile');

            //Shipment Call History
            Route::post('shipment_call_status_history', 'ShipperAppController@shipment_call_status_history')->name('profile');

            //meta api for booking resources
            Route::get('booking_resources','ShipperAppController@booking_resources')->name('booking_resources');

            //shipper pickup addresss and return addresses
            Route::get('pickup_address','ShipperAppController@pickup_address')->name('pickup_address');

            //order management Api
            Route::prefix('orders')->name('orders.')->group(function (){
                Route::get('order_list/{tracking_number?}', 'ShipperOrderManagementApiController@order_list')->name('order_list');
                Route::post('shipment_chart_summary','ShipperOrderManagementApiController@shipments_summary')->name('shipment_chart_summary');
                Route::post('cancel_all', 'ShipperOrderManagementApiController@order_cancel_all')->name('cancel_all');
            });

            //finance Apis
            Route::prefix('finance')->name('finance.')->group(function (){
                  Route::get('v2/payments/{id?}','ShipperFinanceApiController@payment_list')->name('payments');
                  Route::get('v2/GetPaymentShipments','ShipperFinanceApiController@GetPaymentShipments')->name('getPaymentShipments');});

            // CRM Apis
            Route::prefix('crm')->name('crm.')->group(function (){
                Route::get('request_resources','ShipperCrmApiController@crm_request_resources')->name('request_resources');
                Route::post('add_request', 'ShipperCrmApiController@add_crm_request')->name('shipper.crm.add_request');
                Route::get('request_summary', 'ShipperCrmApiController@crm_request_summary')->name('request_summary');
//                Route::get('request_list/{status?}', 'ShipperCrmApiController@crm_request_list')->name('request_list');
                Route::get('request_list', 'ShipperCrmApiController@crm_request_list')->name('request_list');
                Route::post('add_comments', 'ShipperCrmApiController@crm_comment_add')->name('add_comments');
                Route::post('comments_details/{id}', 'ShipperCrmApiController@request_details')->name('comments_details');
                Route::post('single_crm_request','ShipperCrmApiController@single_crm_request')->name('single_crm_request');
                Route::post('receiving_sheet','ShipperCrmApiController@get_receving_sheet')->name('receiving_sheet');
            });


        });
    });

    Route::prefix('botsify')->name('botsify.')->group(function () {
        Route::post('shipper/phone_number', 'APIController@whatsapp_shipper_phone_number')->name('shipper.phone_number');
        Route::post('shipment/tracking', 'APIController@whatsapp_shipper_tracking')->name('shipment.tracking');
        Route::post('crm/launch', 'APIController@whatsapp_crm_request_create')->name('crm.launch');
        Route::post('crm/tracking', 'APIController@whatsapp_shipper_crm_tracking')->name('crm.tracking');
    });

    Route::post('track/google', 'APIController@shipment_google_track')->name('track.google');
    Route::post('live_tracking', 'APIController@live_tracking')->name('live_tracking');
    //Hbl Konnect
    Route::prefix('banking')->name('banking.')->group(function () {
        Route::prefix('hbl_konnect')->name('hbl_konnect.')->group(function () {
            Route::post('delivery_note_information', 'APIController@hbl_konnect_delivery_note_information')->name('delivery_note_information');
            Route::post('transaction_information', 'APIController@hbl_konnect_transactions')->name('transaction_information');

            Route::post('retail_note_information', 'APIController@hbl_konnect_retail_note_cash_collection_information')->name('retail_note_information');
            Route::post('retail_note_transaction_information', 'APIController@hbl_konnect_retail_note_cash_collection_transactions')->name('retail_note_transaction_information');
            Route::post('clone_retail_note_transaction_information2', 'APIController@hbl_konnect_retail_note_cash_collection_transactions_2')->name('retail_note_transaction_information2');

        });
        Route::prefix('easypaisa')->name('easypaisa.')->group(function () {
            Route::post('delivery_note_information', 'APIController@hbl_konnect_delivery_note_information')->name('delivery_note_information');
            Route::post('transaction_information', 'APIController@hbl_konnect_transactions')->name('transaction_information');
        });

        Route::prefix('1link')->name('1link.')->group(function () {
            Route::prefix('payments')->name('payments.')->group(function () {
                Route::post('billinquiry', 'APIController@onelink_payment_billinquiry')->name('billinquiry');
                Route::post('billpayment', 'APIController@onelink_payment_billpayment')->name('billpayment');
                Route::post('out_for_delivery_shipment_payment', 'APIController@out_for_delivery_shipment_payment')->name('out_for_delivery_shipment_payment');
            });
        });
    });


    Route::prefix('1link')->name('1link.')->group(function () {
        Route::post('out_for_delivery_shipment_payment', 'APIController@out_for_delivery_shipment_payment')->name('out_for_delivery_shipment_payment');
    });
    //Hbl Konnect

    Route::prefix('employee')->name('employee.')->group(function () {
        Route::post('app_login', 'APIController@app_login')->name('app_login');
        Route::post('forget_pin', 'APIController@forget_pin')->name('forget_pin');
        Route::post('reset_pin', 'APIController@reset_pin')->name('reset_pin');
    });
});