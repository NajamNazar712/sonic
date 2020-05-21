<?php

namespace App\http\Models\Pickup;

use Illuminate\Database\Eloquent\Model;

class V2PickupRequest extends Model
{
    public function shipper() {
        return $this->belongsTo('App\Http\Models\Shipper\User', 'shipper_id', 'id');
    }

    public function pickup_address() {
        return $this->belongsTo('App\Http\Models\Shipper\UserShippingInfo');
    }

    public function assigned_shipments() {
        return $this->hasMany('App\Http\Models\Pickup\V2PickupRequestAssignedShipment');
    }

    public function reason() {
        return $this->hasOne('App\Http\Models\PickupNotPickReason', 'reason', 'id');
    }
    public function status() {
        return $this->hasOne('App\Http\Models\V2PickupRequestStatus', 'status', 'id');
    }
}
