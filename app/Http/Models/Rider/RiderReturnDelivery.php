<?php

namespace App\Http\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class RiderReturnDelivery extends Model
{

    public function return_note_id() {
        return $this->belongsTo('App\Http\Models\Admin\ReturnNote', 'return_note_id', 'id');
    }

    public function shipment_id() {
        return $this->belongsTo('App\Http\Models\Shipment', 'shipment_id', 'id');
    }

    public function rider_id() {
        return $this->belongsTo('App\Http\Models\Rider', 'rider_id', 'id')->withTrashed();
    }

    public function rider_status_id() {
        return $this->belongsTo('App\Http\Models\ShipmentStatus', 'rider_status_id', 'id');
    }

    public function rider_status_reason_id() {
        return $this->belongsTo('App\Http\Models\ShipmentStatusReason', 'rider_status_reason_id', 'id');
    }
}
