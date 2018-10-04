<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CargoConsignment extends Model
{
	public function cargo_consignment_shipments() {
		return $this->hasMany('App\Http\Models\CargoConsignmentShipment')->orderBy('shipment_id');
	}

	public function origin_hub() {
		return $this->belongsTo('App\Http\Models\City', 'origin_hub_id', 'id');
	}

	public function destination_hub() {
		return $this->belongsTo('App\Http\Models\City', 'destination_hub_id', 'id');
	}

	public function junction_hub_1() {
		return $this->belongsTo('App\Http\Models\City', 'junction_hub_1_id', 'id');
	}

	public function junction_hub_2() {
		return $this->belongsTo('App\Http\Models\City', 'junction_hub_2_id', 'id');
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