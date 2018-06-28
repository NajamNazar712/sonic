<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CargoConsignment extends Model
{
	public function cargo_consignment_shipments() {
		return $this->hasMany('App\Http\Models\CargoConsignmentShipment');
	}

	public function origin_city() {
		return $this->belongsTo('App\Http\Models\City', 'origin_city_id', 'id');
	}

	public function destination_city() {
		return $this->belongsTo('App\Http\Models\City', 'destination_city_id', 'id');
	}

	public function junction_city_1() {
		return $this->belongsTo('App\Http\Models\City', 'junction_city_1_id', 'id');
	}

	public function junction_city_2() {
		return $this->belongsTo('App\Http\Models\City', 'junction_city_2_id', 'id');
	}

	public function hub() {
		return $this->belongsTo('App\Http\Models\City', 'hub_id', 'id');
	}

	public function shipping_mode() {
		return $this->belongsTo('App\Http\Models\ShippingMode');
	}

	public function transport_mode() {
		return $this->belongsTo('App\Http\Models\TransportMode');
	}

	public function transport_mode_vendor() {
		return $this->belongsTo('App\Http\Models\TransportModeVendor');
	}

	public function sender() {
		return $this->belongsTo('App\Http\Models\Admin\Admin', 'sender_id', 'id');
	}

	public function receiver() {
		return $this->belongsTo('App\Http\Models\Admin\Admin', 'receiver_id', 'id');
	}
}