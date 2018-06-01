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
}