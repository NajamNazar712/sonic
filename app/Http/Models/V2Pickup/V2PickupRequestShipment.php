<?php

namespace App\Http\Models\V2Pickup;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class V2PickupRequestShipment extends Model
{

    public function pickup_request() {
        return $this->belongsTo('App\Http\Models\V2Pickup\V2PickupRequest', 'pickup_request_id');
    }

    public function shipment() {
        return $this->belongsTo('App\Http\Models\Shipment');
    }
}
