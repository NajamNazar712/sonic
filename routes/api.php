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
	Route::post('shipment/book', 'APIController@shipment_book')->name('shipment.book');
	Route::get('shipment/status', 'APIController@shipment_status')->name('shipment.status');
	Route::get('shipment/track', 'APIController@shipment_track')->name('shipment.track');
});