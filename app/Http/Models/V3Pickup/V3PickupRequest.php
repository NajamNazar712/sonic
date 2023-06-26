<?php

namespace App\Http\Models\V3Pickup;

use Illuminate\Database\Eloquent\Model;

class V3PickupRequest extends Model
{

    public function pickup_address() {
        return $this->belongsTo('App\Http\Models\Shipper\UserShippingInfo, pickup_address_id');
    }

}
