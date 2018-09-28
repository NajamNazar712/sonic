<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PickupRequest extends Model
{
	public function shipper() {
		return $this->belongsTo('App\Http\Models\Shipper\User', 'shipper_id', 'id');
	}

	public function pickup_address() {
		return $this->belongsTo('App\Http\Models\Shipper\UserShippingInfo');
	}

	public function pickup_note_request() {
		return $this->hasOne('App\Http\Models\PickupNoteRequest');
	}

	public function pickup_request_assigned_shipments() {
		return $this->hasMany('App\Http\Models\PickupRequestAssignedShipment');
	}

	public function pickup_request_short_received_shipments() {
		return $this->hasMany('App\Http\Models\PickupRequestShortReceivedShipment');
	}
}