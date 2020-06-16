<?php

namespace App\Http\Models\Handover;

use Illuminate\Database\Eloquent\Model;

class HandoverShipmentsJourney extends Model
{

    protected $table = 'handover_shipments_journeys';
    protected $fillable = ['shipment_id', 'status', 'handover_id'];
    
    public function status() {
    	return $this->belongsTo('App\Http\Models\Handover\HandoverShipmentStatus', 'status');
    }
}
