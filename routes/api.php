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

Route::middleware('APIToken')->name('api.')->group(function () {
	Route::get('pickup_addresses', 'APIController@pickup_addresses')->name('pickup_addresses');
	Route::post('pickup_address/add', 'APIController@pickup_address_add')->name('pickup_address.add');

	Route::post('shipment/book', 'APIController@shipment_book')->name('shipment.book');

	Route::get('shipment/status', 'APIController@shipment_status')->name('shipment.status');
	Route::get('shipment/track', 'APIController@shipment_track')->name('shipment.track');

	Route::get('cities', 'APIController@cities')->name('cities');
});