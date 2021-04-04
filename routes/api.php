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

	Route::middleware('APIToken')->group(function() {
		Route::post('verify', 'APIController@verify')->name('verify');

		Route::get('pickup_addresses', 'APIController@pickup_addresses')->name('pickup_addresses');
		Route::post('pickup_address/add', 'APIController@pickup_address_add')->name('pickup_address.add');

		Route::prefix('shipment')->name('shipment.')->group(function() {
			Route::post('book', 'APIController@shipment_book')->name('book');
			Route::post('book/gul_ahmed', 'APIController@shipment_book_gul_ahmed')->name('book.gul_ahmed');
			Route::get('air_waybill', 'APIController@shipment_air_waybill')->name('air_waybill');
			Route::get('status', 'APIController@shipment_status')->name('status');

            Route::prefix('track')->name('track.')->group(function() {
                Route::get('', 'APIController@shipment_track')->name('track');
                Route::get('order_id', 'APIController@shipment_track_order_id')->name('order_id');
            });

			Route::get('charges', 'APIController@shipment_charges')->name('charges');
			Route::get('payment_status', 'APIController@shipment_payment_status')->name('payment_status');
			Route::get('payments', 'APIController@shipment_payments')->name('payments');
			Route::post('cancel', 'APIController@shipment_cancel')->name('cancel');
		});

		Route::prefix('receiving_sheet')->name('receiving_sheet.')->group(function() {
			Route::post('create', 'APIController@receiving_sheet_create')->name('create');
			Route::get('view', 'APIController@receiving_sheet_view')->name('view');
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

	});

	Route::middleware('APIThrottle:25,0.5')->prefix('shipment')->name('shipment.')->group(function() {
		Route::get('track/public', 'APIController@shipment_track_public')->name('track.public');
	});


	Route::prefix('rider')->name('rider.')->group(function() {
		Route::post('login', 'Rider\RiderAPIController@login')->name('login');
		Route::post('login_v2', 'Rider\RiderAPIController@login_v2')->name('login_v2');
		Route::get('slider', 'Rider\RiderAPIController@rider_ticker_images')->name('slider');
		Route::any('signup', 'Rider\RiderAPIController@rider_signup')->name('signup');
        Route::get('cities', 'Rider\RiderAPIController@cities')->name('cities');
        Route::prefix('register_request')->name('register_request.')->group(function () {
            Route::get('signup_data', 'Rider\RiderAPIController@signup_data')->name('signup_data');
            Route::post('store', 'Rider\RiderAPIController@rider_signup_v2')->name('store');
            Route::post('attachment_store', 'Rider\RiderAPIController@rider_attachments_store')->name('attachment_store');
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
	        });

            Route::prefix('location')->name('location.')->group(function(){
                Route::post('','Rider\RiderAPIController@get_rider_location')->name('get');
            });

            Route::prefix('delivery')->name('delivery.')->group(function () {
                Route::get('summary', 'Rider\RiderAPIController@delivery_summary')->name('delivery_summary');
                Route::post('action_log', 'Rider\RiderAPIController@delivery_action_log')->name('delivery_action_log');
                Route::post('delivered', 'Rider\RiderAPIController@shipment_delivered')->name('delivered');
                Route::post('undelivered', 'Rider\RiderAPIController@shipment_undelivered')->name('undelivered');
                Route::get('summary/multiple', 'Rider\RiderAPIController@delivery_summary_multiple')->name('delivery_summary_multiple');
                Route::get('summary/multiple_v2', 'Rider\RiderAPIController@delivery_summary_multiple_v2')->name('delivery_summary_multiple_v2');
                Route::post('undelivered_v2', 'Rider\RiderAPIController@shipment_undelivered_v2')->name('undelivered_v2');
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
                Route::get('summary_v2', 'Rider\RiderAPIController@return_summary_multiple_v2')->name('summary_v2');
            });

            Route::get('rider_wallet', 'Rider\RiderAPIController@rider_wallet')->name('rider_wallet');
            Route::get('rider_profile', 'Rider\RiderAPIController@rider_profile')->name('rider_profile');

            Route::prefix('attendance')->name('attendance.')->group(function() {
                Route::post('detail', 'Rider\RiderAPIController@attendance_details')->name('detail');
                Route::post('mark', 'Rider\RiderAPIController@mark_attendance')->name('mark');
                Route::post('history', 'Rider\RiderAPIController@attendance_history')->name('history');
            });

		});

	});

    Route::prefix('admin')->name('admin.')->group(function() {
        Route::post('login', 'AdminAPIController@login')->name('login');
        Route::post('login_v2', 'AdminAPIController@login')->name('login_v2');
        Route::get('slider', 'Rider\RiderAPIController@rider_ticker_images')->name('slider');
        Route::prefix('register_request')->name('register_request.')->group(function () {
            Route::get('signup_data', 'Rider\RiderAPIController@signup_data')->name('signup_data');
            Route::post('store', 'AdminAPIController@admin_signup')->name('store');
            Route::post('attachment_store', 'AdminAPIController@admin_attachments_store')->name('attachment_store');
        });

        Route::middleware('AdminAPIToken')->group(function () {
            Route::post('verify', 'AdminAPIController@verify')->name('verify');
            Route::post('return_note_details', 'AdminAPIController@return_note_details')->name('return_note_details');
            Route::post('history_update_image', 'AdminAPIController@history_update_image')->name('history_update_image');

            Route::prefix('attendance')->name('attendance.')->group(function() {
                Route::post('detail', 'AdminAPIController@attendance_details')->name('detail');
                Route::post('mark', 'AdminAPIController@mark_attendance')->name('mark');
                Route::post('history', 'AdminAPIController@attendance_history')->name('history');
            });

        });

    });



});