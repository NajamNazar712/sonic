<?php

namespace App\Http\Models\V2Pickup;

use Illuminate\Database\Eloquent\Model;

class V2RiderPickup extends Model
{
    public function pickup_request() {
        return $this->belongsTo('App\Http\Models\V2Pickup\V2PickupRequest', 'pickup_request_id')->groupBy('current_rider_id');
    }
}
