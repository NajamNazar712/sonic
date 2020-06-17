<?php

namespace App\Http\Models\Handover;

use Illuminate\Database\Eloquent\Model;

class HandoverShipmentsJourney extends Model
{   
    
    public function status() {
    	return $this->belongsTo('App\Http\Models\Handover\HandoverShipmentStatus', 'status', 'id');
    }
}
