<?php

namespace App\Http\Models\V3Pickup;

use Illuminate\Database\Eloquent\Model;

class ShipmentsV3PickupJourney extends Model
{
    protected $table = 'shipments_v3_pickup_journeys';
    protected $fillable = ['shipment_id', 'status_id', 'admin_id'];

    public function status()
    {
        return $this->belongsTo('App\Http\Models\V3Pickup\V3PickupRequestStatus', 'status_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'admin_id', 'id');
    }

    public function reason()
    {
        return $this->belongsTo('App\Http\Models\V3Pickup\V3PickupRequestReason', 'reason_id', 'id');
    }
}
