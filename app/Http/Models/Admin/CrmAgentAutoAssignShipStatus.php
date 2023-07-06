<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CrmAgentAutoAssignShipStatus extends Model
{
    public function shipment_statuses() {
        return $this->belongsTo('App\Http\Models\ShipmentStatus', 'shipment_status_id', 'id');
    }
}
