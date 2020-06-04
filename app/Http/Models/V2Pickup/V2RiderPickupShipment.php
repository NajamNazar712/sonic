<?php

namespace App\Http\Models\V2Pickup;

use Illuminate\Database\Eloquent\Model;

class V2RiderPickupShipment extends Model
{
    public function shipment() {
        return $this->belongsTo('App\Http\Models\Shipment');
    }
}
