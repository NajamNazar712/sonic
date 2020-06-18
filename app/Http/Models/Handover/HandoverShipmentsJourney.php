<?php

namespace App\Http\Models\Handover;

use Illuminate\Database\Eloquent\Model;

class HandoverShipmentsJourney extends Model
{   
    public function my_status() {
        return $this->hasOne('App\Http\Models\Handover\HandoverShipmentStatus', 'id', 'status');
    }
}
