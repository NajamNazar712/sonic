<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
	public function items() {
		return $this->hasMany('App\Http\Models\ShipmentItem');
	}

	public function booking_type() {
		return $this->belongsTo('App\Http\Models\BookingType');
	}

	public function pickup_address() {
		return $this->belongsTo('App\Http\Models\Shipper\UserShippingInfo');
	}

	public function consignee_city() {
		return $this->belongsTo('App\Http\Models\CityInfo', 'consignee_city_id', 'id');
	}

	public function user() {
		return $this->belongsTo('App\Http\Models\Shipper\User');
	}

	public function payment_mode() {
		return $this->belongsTo('App\Http\Models\PaymentMode');
	}
}