<?php

namespace App\Http\Models\V2Pickup;

use Illuminate\Database\Eloquent\Model;

class ShipmentsV2PickupJourney extends Model
{
    protected $table = 'shipments_v2_pickup_journeys';
	protected $fillable = ['shipment_id', 'status_id', 'admin_id'];

    public function status() {
    	return $this->belongsTo('App\Http\Models\V2Pickup\V2PickupRequestStatus', 'status_id', 'id');
    }

    public function admin() {
    	return $this->belongsTo('App\Http\Models\Admin\Admin', 'admin_id', 'id');
    }

    public function reason() {
        return $this->belongsTo('App\Http\Models\V2Pickup\V2PickupRequestNotPickReason', 'reason_id', 'id');
    }
}
