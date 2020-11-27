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
    public function pickup_city() {
        return $this->belongsTo('App\Http\Models\City', 'city_id');
    }
    public function pickup_request_shipments() {
        return $this->hasMany('App\Http\Models\V2Pickup\V2PickupRequestShipment', 'pickup_request_id');
    }
    public function pickup_request_received_shipments() {
        return $this->hasMany('App\Http\Models\V2Pickup\V2PickupReceivedShipment', 'pickup_request_id');
    }
    public function pickup_attempts(){
        return $this->hasMany('App\Http\Models\V2Pickup\V2PickupRequestAttempt', 'pickup_request_id');
    }
    public function pickup_attempt_latest(){
        return $this->hasOne('App\Http\Models\V2Pickup\V2PickupRequestAttempt', 'pickup_request_id')->latest('id');
    }
    public function pickup_note_request(){
        return $this->belongsTo('App\Http\Models\V2Pickup\V2PickupNoteRequest','id','pickup_request_id')->latest('id');
    }
    public function last_admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'last_updated_by', 'id');
    }
    protected $guarded = [];
}
