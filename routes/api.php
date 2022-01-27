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
	Route::post('login', 'APIController@login')->name('login');
	Route::post('user_login', 'APIController@bolt_login')->name('user_login');
	Route::post('forget_pin', 'APIController@bolt_forget_pin')->name('forget_pin');
	Route::post('reset_pin', 'APIController@bolt_reset_pin')->name('reset_pin');
	Route::post('store_device_token', 'APIController@store_device_token')->name('store_device_token');
	Route::post('delete_device_token', 'APIController@delete_device_token')->name('delete_device_token');

	Route::middleware('APIToken')->group(function() {
		Route::post('verify', 'APIController@verify')->name('verify');

		Route::get('pickup_addresses', 'APIController@pickup_addresses')->name('pickup_addresses');
		Route::post('pickup_address/add', 'APIController@pickup_address_add')->name('pickup_address.add');

		Route::prefix('shipment')->name('shipment.')->group(function() {
			Route::post('book', 'APIController@shipment_book')->name('book');
			Route::post('book/gul_ahmed', 'APIController@shipment_book_gul_ahmed')->name('book.gul_ahmed');
			Route::get('air_waybill', 'APIController@shipment_air_waybill')->name('air_waybill');

            Route::prefix('status')->name('status.')->group(function() {
                Route::get('', 'APIController@shipment_status')->name('status');
                Route::get('order_id', 'APIController@shipment_status_order_id')->name('order_id');
                Route::get('consingee_phone_number', 'APIController@shipment_status_consingee_phone_number')->name('consingee_phone_number');
            });

            Route::prefix('track')->name('track.')->group(function() {
                Route::get('', 'APIController@shipment_track')->name('track');
                Route::get('order_id', 'APIController@shipment_track_order_id')->name('order_id');
            });

			Route::get('charges', 'APIController@shipment_charges')->name('charges');
			Route::get('payment_status', 'APIController@shipment_payment_status')->name('payment_status');
			Route::get('payments', 'APIController@shipment_payments')->name('payments');
			Route::post('cancel', 'APIController@shipment_cancel')->name('cancel');

			Route::post('eta', 'APIController@shipment_status_eta')->name('eta');

		});
		Route::prefix('request')->name('request.')->group(function() {
			Route::post('crm', 'APIController@crm_request_create')->name('crm');
			Route::post('rcp', 'APIController@rcp_request_create')->name('rcp');

        });

		Route::post('pickup_address/add', 'APIController@pickup_address_add')->name('pickup_address.add');

		Route::prefix('receiving_sheet')->name('receiving_sheet.')->group(function() {
			Route::post('create', 'APIController@receiving_sheet_create')->name('create');
			Route::get('view', 'APIController@receiving_sheet_view')->name('view');
            Route::post('add', 'APIController@receiving_sheet_add')->name('add');
            Route::post('remove', 'APIController@receiving_sheet_void')->name('remove');
            Route::post('cancel', 'APIController@receiving_sheet_cancel')->name('cancel');
		});

		Route::get('cities', 'APIController@cities')->name('cities');

		Route::post('charges_calculate', 'APIController@charges_calculate')->name('charges_calculate');
		Route::post('consolidate', 'APIController@shipment_consolidate')->name('consolidate');
        Route::prefix('return')->name('return.')->group(function (){
            Route::get('pending', 'APIController@return_confirmation_pending')->name('pending');
            Route::post('pending', 'APIController@return_confirmation_pending_update')->name('pending');

        });


        Route::prefix('shopify')->name('shopify.')->group(function() {
            Route::post('invoice', 'ShopifyController@invoice_settings')->name('invoice');
            Route::post('air_waybill', 'APIController@shipment_air_waybill_shopify_invoice')->name('air_waybill');
        });

		Route::get('catalyst_users', 'APIController@catalyst_users')->name('catalyst_users');

		Route::post('payments', 'APIController@payments')->name('payments');

		Route::post('invoice', 'APIController@invoice')->name('invoice');


	});

	Route::middleware('APIThrottle:500,0.5')->prefix('shipment')->name('shipment.')->group(function() {
		Route::get('track/public', 'APIController@shipment_track_public')->name('track.public');
	});


	Route::prefix('rider')->name('rider.')->group(function() {
		Route::post('login', 'Rider\RiderAPIController@login')->name('login');
        Route::post('login_v2', 'Rider\RiderAPIController@login_v2')->name('login_v2');
        Route::post('login_v3', 'Rider\RiderAPIController@login_v2')->name('login_v3');
        Route::get('slider', 'Rider\RiderAPIController@rider_ticker_images')->name('slider');
        Route::any('signup', 'Rider\RiderAPIController@rider_signup')->name('signup');
        Route::get('cities', 'Rider\RiderAPIController@cities')->name('cities');
        Route::get('shipment_settings', 'Rider\RiderAPIController@shipment_attempt_settings')->name('shipment_settings');
        Route::post('forget_pin', 'Rider\RiderAPIController@forget_pin')->name('forget_pin');
        Route::post('reset_pin', 'Rider\RiderAPIController@reset_pin')->name('reset_pin');
        Route::get('check_pin', 'Rider\RiderAPIController@check_pin')->name('check_pin');
        Route::get('logout', 'Rider\RiderAPIController@logout')->name('logout');

        Route::prefix('register_request')->name('register_request.')->group(function () {
            Route::get('signup_data', 'Rider\RiderAPIController@signup_data')->name('signup_data');
            Route::post('validate_data', 'Rider\RiderAPIController@validate_cnic_phone_number')->name('validate_data');
            Route::prefix('store_v3')->name('store_v3.')->group(function () {
                Route::post('required_details', 'Rider\RiderAPIController@signup_required_details')->name('required_details');
                Route::post('optional_details', 'Rider\RiderAPIController@signup_optional_details')->name('optional_details');
            });
            Route::post('store', 'Rider\RiderAPIController@rider_signup_v2')->name('store');
            Route::post('store_v2', 'Rider\RiderAPIController@rider_signup_v3')->name('store_v3');
            Route::post('attachment_store', 'Rider\RiderAPIController@rider_attachments_store')->name('attachment_store');
            Route::post('attachment_store_v2', 'Rider\RiderAPIController@rider_attachments_store_v2')->name('attachment_store_v2');
            Route::post('attachment_view', 'Rider\RiderAPIController@rider_attachments_view')->name('attachment_view');
            Route::post('attachment_delete', 'Rider\RiderAPIController@rider_attachments_delete')->name('attachment_delete');
        });

		Route::middleware('RiderAPIToken')->group(function () {
			Route::prefix('pickup')->name('pickup.')->group(function () {
                Route::get('summary', 'Rider\RiderAPIController@pickup_summary')->name('pickup_summary');
                Route::post('pick', 'Rider\RiderAPIController@pickup_pick')->name('pickup_pick');
                Route::post('not_pick', 'Rider\RiderAPIController@pickup_not_pick')->name('pickup_not_pick');
				Route::post('action_log', 'Rider\RiderAPIController@pickup_action_log')->name('pickup_action_log');
				//pickup revamp module
				Route::get('summary_v2', 'Rider\RiderAPIController@pickup_summary_v2')->name('pickup_summary_v2');
                Route::post('pick_v2', 'Rider\RiderAPIController@pickup_pick_v2')->name('pickup_pick_v2');
                Route::post('not_pick_v2', 'Rider\RiderAPIController@pickup_not_pick_v2')->name('pickup_not_pick_v2');
                Route::post('action_log_v2', 'Rider\RiderAPIController@pickup_action_log_v2')->name('pickup_action_log_v2');
                Route::post('not_pick_v3', 'Rider\RiderAPIController@pickup_not_pick_v3')->name('pickup_not_pick_v3');

                Route::post('check_tracking_number', 'Rider\RiderAPIController@pickup_check_tracking_number')->name('check_tracking_number');

                Route::post('scan_shipment_assign', 'Rider\RiderAPIController@scan_shipment_assign')->name('scan_shipment_assign');
                Route::post('scan_shipment_detail', 'Rider\RiderAPIController@scan_shipment_detail')->name('scan_shipment_detail');

                Route::post('pickup_in_route', 'Rider\RiderAPIController@pickup_in_route')->name('pickup_in_route');
	        });

            Route::prefix('location')->name('location.')->group(function(){
                Route::post('','Rider\RiderAPIController@get_rider_location')->name('get');
            });
            Route::post('location_v2', 'Rider\RiderAPIController@get_rider_location_v2')->name('location_v2');

            Route::prefix('delivery')->name('delivery.')->group(function () {
                Route::get('summary', 'Rider\RiderAPIController@delivery_summary')->name('delivery_summary');
                Route::post('action_log', 'Rider\RiderAPIController@delivery_action_log')->name('delivery_action_log');
                Route::post('delivered', 'Rider\RiderAPIController@shipment_delivered')->name('delivered');
                Route::post('undelivered', 'Rider\RiderAPIController@shipment_undelivered')->name('undelivered');
                Route::get('summary/multiple', 'Rider\RiderAPIController@delivery_summary_multiple')->name('delivery_summary_multiple');
                Route::get('summary/multiple_v2', 'Rider\RiderAPIController@delivery_summary_multiple_v2')->name('delivery_summary_multiple_v2');
                Route::get('summary/multiple_v3', 'Rider\RiderAPIController@delivery_summary_multiple_v3')->name('delivery_summary_multiple_v3');
                Route::get('summary/multiple_v4', 'Rider\RiderAPIController@delivery_summary_multiple_v4')->name('delivery_summary_multiple_v4');
                Route::get('summary/multiple_v5', 'Rider\RiderAPIController@delivery_summary_multiple_v5')->name('delivery_summary_multiple_v5');
                Route::post('undelivered_v2', 'Rider\RiderAPIController@shipment_undelivered_v2')->name('undelivered_v2');
                Route::post('delivered_v2', 'Rider\RiderAPIController@shipment_delivered_v2')->name('delivered_v2');
                Route::post('delivery_in_route', 'Rider\RiderAPIController@delivery_in_route')->name('delivery_in_route');
                Route::post('delivered_v3', 'Rider\RiderAPIController@shipment_delivered_v3')->name('delivered_v3');
                Route::post('delivered_v4', 'Rider\RiderAPIController@shipment_delivered_v4')->name('delivered_v4');
                Route::post('delivered_v5', 'Rider\RiderAPIController@shipment_delivered_v5')->name('delivered_v5');
            });
            Route::prefix('comments')->name('comments.')->group(function () {
                Route::post('add', 'Rider\RiderAPIController@crm_comment_add')->name('add');
            });


            Route::prefix('history')->name('history.')->group(function () {
                Route::post('pickup', 'Rider\RiderAPIController@pickups_history')->name('pickup');
                Route::post('delivery', 'Rider\RiderAPIController@delivery_history')->name('delivery');
                Route::post('return', 'Rider\RiderAPIController@return_history')->name('return');

                Route::post('pickup_v2', 'Rider\RiderAPIController@pickups_history_v2')->name('pickup_v2');
                Route::post('delivery_v2', 'Rider\RiderAPIController@delivery_history_v2')->name('delivery_v2');
                Route::post('return_v2', 'Rider\RiderAPIController@return_history_v2')->name('return_v2');
                Route::post('history_details', 'Rider\RiderAPIController@history_details')->name('history_details');
            });

            Route::prefix('return')->name('return.')->group(function () {
                Route::get('summary', 'Rider\RiderAPIController@return_summary_multiple')->name('return_summary');
                Route::post('delivered', 'Rider\RiderAPIController@return_shipment_delivered')->name('delivered');
                Route::post('undelivered', 'Rider\RiderAPIController@return_shipment_undelivered')->name('undelivered');
                Route::post('action_log', 'Rider\RiderAPIController@return_action_log')->name('action_log');
                Route::post('undelivered_v2', 'Rider\RiderAPIController@return_shipment_undelivered_v2')->name('undelivered_v2');
                Route::post('undelivered_v3', 'Rider\RiderAPIController@return_shipment_undelivered_v3')->name('undelivered_v3');
                Route::post('undelivered_v4', 'Rider\RiderAPIController@return_shipment_undelivered_v4')->name('undelivered_v4');
                Route::get('summary_v2', 'Rider\RiderAPIController@return_summary_multiple_v2')->name('summary_v2');
                Route::post('delivered_v2', 'Rider\RiderAPIController@return_shipment_delivered_v2')->name('delivered_v2');
            });

            Route::get('rider_wallet', 'Rider\RiderAPIController@rider_wallet')->name('rider_wallet');
            Route::get('rider_profile', 'Rider\RiderAPIController@rider_profile')->name('rider_profile');
            Route::get('profile', 'Rider\RiderAPIController@rider_profile')->name('profile');
            Route::get('notification_history', 'Rider\RiderAPIController@notification_history')->name('notification_history');

            Route::prefix('attendance')->name('attendance.')->group(function() {
                Route::get('shift', 'Rider\RiderAPIController@rider_shift')->name('shift');
                Route::post('month_history', 'Rider\RiderAPIController@month_attendance_history')->name('month_history');
                Route::post('month_history_v2', 'Rider\RiderAPIController@month_attendance_history_v2')->name('month_history_v2');
                Route::post('detail', 'Rider\RiderAPIController@attendance_details')->name('detail');
                Route::post('mark', 'Rider\RiderAPIController@mark_attendance')->name('mark');
                Route::post('history', 'Rider\RiderAPIController@attendance_history')->name('history');
                Route::post('mark_v2', 'Rider\RiderAPIController@mark_attendance_v2')->name('mark_v2');
                Route::post('detail_v2', 'Rider\RiderAPIController@attendance_details_v2')->name('detail_v2');
            });

            Route::post('rider_incentives', 'Rider\RiderAPIController@rider_incentive')->name('rider_incentives');
            Route::post('rider_incentives_v2', 'Rider\RiderAPIController@rider_incentive_v2')->name('rider_incentives_v2');
            Route::post('rider_incentives_v3', 'Rider\RiderAPIController@rider_incentive_v3')->name('rider_incentives_v3');

            Route::prefix('retail')->name('retail.')->group(function () {
                Route::get('retail_data', 'Rider\RiderAPIController@retail_index')->name('retail_data');
                Route::post('retail_bank_info', 'Rider\RiderAPIController@retail_bank_info')->name('retail_bank_info');
                Route::post('retail_shipment_store', 'Rider\RiderAPIController@retail_shipment_store')->name('retail_shipment_store');
            });

            Route::prefix('profile')->name('profile.')->group(function () {
                Route::get('index', 'Rider\RiderAPIController@get_profile')->name('index');
                Route::get('check', 'Rider\RiderAPIController@check_profile')->name('check');
                Route::post('update', 'Rider\RiderAPIController@update_profile')->name('update');
            });

            Route::post('payslip', 'Rider\RiderAPIController@rider_payslip')->name('payslip');
            Route::post('fake_status', 'Rider\RiderAPIController@fake_status_count')->name('fake_status');

            Route::prefix('leave')->name('leave.')->group(function () {
                Route::post('index', 'Rider\RiderAPIController@leave_index')->name('index');
                Route::post('apply', 'Rider\RiderAPIController@leave_apply')->name('apply');
                Route::get('list', 'Rider\RiderAPIController@employee_leave_list')->name('list');
                Route::post('calender', 'Rider\RiderAPIController@view_calender')->name('calender');
            });

            Route::get('employee_id', 'Rider\RiderAPIController@get_employee_id')->name('employee_id');
		});

	});

    Route::prefix('admin')->name('admin.')->group(function() {
        Route::post('login', 'AdminAPIController@login')->name('login');
        Route::post('login_v2', 'AdminAPIController@login')->name('login_v2');
        Route::get('slider', 'Rider\RiderAPIController@rider_ticker_images')->name('slider');
        Route::get('slider', 'AdminAPIController@admin_ticker_images')->name('slider');
        Route::post('login_v3', 'AdminAPIController@login_v3')->name('login_v3');        Route::post('forget_password','AdminAPIController@forget_password')->name('forget_password');
        Route::post('forget_pin', 'AdminAPIController@forget_pin')->name('forget_pin');
        Route::post('reset_pin', 'AdminAPIController@reset_pin')->name('reset_pin');
        Route::get('check_pin', 'AdminAPIController@check_pin')->name('check_pin');
        Route::get('logout', 'AdminAPIController@logout')->name('logout');
        Route::prefix('register_request')->name('register_request.')->group(function () {
            Route::get('signup_data', 'Rider\RiderAPIController@signup_data')->name('signup_data');
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
        });

        Route::middleware('AdminAPIToken')->group(function () {
            Route::post('verify', 'AdminAPIController@verify')->name('verify');
            Route::post('return_note_details', 'AdminAPIController@return_note_details')->name('return_note_details');
            Route::post('history_update_image', 'AdminAPIController@history_update_image')->name('history_update_image');

            Route::prefix('attendance')->name('attendance.')->group(function() {
                Route::get('shift', 'AdminAPIController@employee_shift')->name('shift');
                Route::post('month_history', 'AdminAPIController@month_attendance_history')->name('month_history');
                Route::post('month_history_v2', 'AdminAPIController@month_attendance_history_v2')->name('month_history_v2');
                Route::post('detail', 'AdminAPIController@attendance_details')->name('detail');
                Route::post('mark', 'AdminAPIController@mark_attendance')->name('mark');
                Route::post('history', 'AdminAPIController@attendance_history')->name('history');
                Route::post('mark_v2', 'AdminAPIController@mark_attendance_v2')->name('mark_v2');
                Route::post('detail_v2', 'AdminAPIController@attendance_details_v2')->name('detail_v2');
                Route::post('mark_api', 'AdminAPIController@mark_attendance_api')->name('mark_api');
                Route::get('flutter_detail', 'AdminAPIController@flutter_attendance_details')->name('flutter_detail');
                Route::post('flutter_mark', 'AdminAPIController@flutter_mark_attendance')->name('flutter_mark');
            });

            Route::prefix('retail')->name('retail.')->group(function () {
                Route::get('retail_data', 'AdminAPIController@retail_index')->name('retail_data');
                Route::post('retail_bank_info', 'AdminAPIController@retail_bank_info')->name('retail_bank_info');
                Route::post('retail_shipment_store', 'AdminAPIController@retail_shipment_store')->name('retail_shipment_store');
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
            });
            Route::prefix('profile')->name('profile.')->group(function () {
                Route::get('index', 'AdminAPIController@get_profile')->name('index');
                Route::get('check', 'AdminAPIController@check_profile')->name('check');
                Route::post('update', 'AdminAPIController@update_profile')->name('update');
            });
            Route::get('profile', 'AdminAPIController@admin_profile')->name('profile');
            Route::get('employee_id', 'AdminAPIController@get_employee_id')->name('employee_id');
            Route::post('dws_weight', 'AdminAPIController@dws_weight')->name('dws_weight');
            Route::post('trax_directory', 'AdminAPIController@trax_directory')->name('trax_directory');
            Route::post('trax_directory_v2', 'AdminAPIController@trax_directory_v2')->name('trax_directory_v2');

            Route::prefix('leads')->name('leads.')->group(function () {
                Route::post('list', 'AdminAPIController@leads_list')->name('list');
                Route::post('add_remarks', 'AdminAPIController@add_remarks')->name('add_remarks');
                Route::post('view_remarks', 'AdminAPIController@view_remarks')->name('view_remarks');
            });

        });

    });

    Route::prefix('retail_user')->name('retail_user.')->group(function() {
        Route::post('login_v2', 'Retail\RetailAPIController@login')->name('login_v2');
        Route::get('slider', 'Retail\RetailAPIController@retail_ticker_images')->name('slider');

        Route::middleware('RetailUserAPIToken')->group(function () {
            Route::prefix('retail')->name('retail.')->group(function () {
                Route::get('retail_data', 'Retail\RetailAPIController@retail_index')->name('retail_data');
                Route::post('retail_bank_info', 'Retail\RetailAPIController@retail_bank_info')->name('retail_bank_info');
                Route::post('retail_shipment_store', 'Retail\RetailAPIController@retail_shipment_store')->name('retail_shipment_store');
            });

        });

    });

    Route::prefix('consignee')->name('consignee.')->group(function() {
        Route::post('get_info', 'ConsigneeAPIController@consignee_info')->name('get_info');
        Route::post('test', 'ConsigneeAPIController@test')->name('test');
        Route::post('consignee_otp', 'ConsigneeAPIController@consignee_otp')->name('consignee_otp');
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
        Route::post('test', 'ShipperAPIController@test')->name('test');
        Route::middleware('ShipperAPIToken')->group(function () {
            Route::post('shipment_history', 'ShipperAPIController@shipment_history')->name('shipment_history');

            Route::prefix('subscription')->name('subscription.')->group(function () {
                Route::get('list', 'ShipperAPIController@shipper_subscription_list')->name('list');
                Route::post('delete', 'ShipperAPIController@shipper_subscription_delete')->name('delete');
            });
            Route::get('notification_history', 'ShipperAPIController@notification_history')->name('notification_history');

            Route::prefix('add_request')->name('add_request.')->group(function () {
                Route::get('index', 'ShipperAPIController@add_request_index')->name('index');
                Route::post('submit', 'ShipperAPIController@add_request_submit')->name('submit');
                Route::post('lost_claim', 'ShipperAPIController@lost_claim')->name('lost_claim');
            });
        });

    });

    Route::post('track/google', 'APIController@shipment_google_track')->name('track.google');
    Route::post('live_tracking', 'APIController@live_tracking')->name('live_tracking');

    
});