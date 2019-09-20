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
	Route::middleware('APIToken')->group(function() {
		Route::post('verify', 'APIController@verify')->name('verify');

		Route::get('pickup_addresses', 'APIController@pickup_addresses')->name('pickup_addresses');
		Route::post('pickup_address/add', 'APIController@pickup_address_add')->name('pickup_address.add');

		Route::prefix('shipment')->name('shipment.')->group(function() {
			Route::post('book', 'APIController@shipment_book')->name('book');
			Route::get('air_waybill', 'APIController@shipment_air_waybill')->name('air_waybill');
			Route::get('status', 'APIController@shipment_status')->name('status');
			Route::get('track', 'APIController@shipment_track')->name('track');
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
	});

	Route::middleware('APIThrottle:25,0.5')->prefix('shipment')->name('shipment.')->group(function() {
		Route::get('track/public', 'APIController@shipment_track_public')->name('track.public');
	});
});