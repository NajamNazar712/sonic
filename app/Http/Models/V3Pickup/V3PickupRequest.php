<?php

namespace App\Http\Models\V3Pickup;

use Illuminate\Database\Eloquent\Model;

class V3PickupRequest extends Model
{

    public function pickup_address() {
        return $this->belongsTo('App\Http\Models\Shipper\UserShippingInfo, pickup_address_id');
    }

    public function pickup_note_request(){
        return $this->belongsTo('App\Http\Models\V3Pickup\V3PickupNoteRequest','id','pickup_request_id')->latest('id');
    }
}
