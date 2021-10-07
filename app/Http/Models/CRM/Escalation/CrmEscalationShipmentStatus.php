<?php

namespace App\Http\Models\CRM\Escalation;

use Illuminate\Database\Eloquent\Model;

class CrmEscalationShipmentStatus extends Model
{
    public function status() {
        return $this->belongsTo('App\Http\Models\ShipmentStatus', 'shipment_status_id', 'id');
    }
}
