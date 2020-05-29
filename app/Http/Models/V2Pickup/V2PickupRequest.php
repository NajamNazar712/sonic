<?php

namespace App\Http\Models\V2Pickup;

use Illuminate\Database\Eloquent\Model;

class V2PickupRequest extends Model
{
    public function shipper() {
		return $this->belongsTo('App\Http\Models\Shipper\User', 'shipper_id', 'id');
    }
    public function rider() {
        return $this->belongsTo('App\Http\Models\Rider','current_rider_id','id');
    }
    public function pickup_address() {
        return $this->belongsTo('App\Http\Models\Shipper\UserShippingInfo');
    }
    public function pickup_request_shipments() {
        return $this->hasMany('App\Http\Models\V2Pickup\V2PickupRequestShipment', 'pickup_request_id');
    }
}
