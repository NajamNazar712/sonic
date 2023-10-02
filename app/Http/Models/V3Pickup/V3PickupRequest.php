<?php

namespace App\Http\Models\V3Pickup;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class V3PickupRequest extends Model
{


    public function getPickupDateAttribute(){
        return Carbon::parse($this->attributes['pickup_date'])->toDateString();
    }
    public function pickup_address() {
        return $this->belongsTo('App\Http\Models\Shipper\UserShippingInfo');
    }

    public function pickup_note_request(){
        return $this->belongsTo('App\Http\Models\V3Pickup\V3PickupNoteRequest','id','pickup_request_id')->latest('id');
    }
    public function pickup_request_services(){
        return $this->belongsToMany(V3PickupService::class,'v3_pickup_request_services','pickup_request_id','pickup_request_service_id')
        ->withPivot('count');
    }

    public function rider() {
        return $this->belongsTo('App\Http\Models\Rider','current_rider_id','id');
    }
}
