<?php

namespace App\Http\Models\V2Pickup;

use Illuminate\Database\Eloquent\Model;

class V2PickupRequest extends Model
{
    public function pickup_address() {
        return $this->belongsTo('App\Http\Models\Shipper\UserShippingInfo');
    }
    public function shipper() {
		return $this->belongsTo('App\Http\Models\Shipper\User','shipper_id');
	}
}
