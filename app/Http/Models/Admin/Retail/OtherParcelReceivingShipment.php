<?php

namespace App\Http\Models\Admin\Retail;

use Illuminate\Database\Eloquent\Model;

class OtherParcelReceivingShipment extends Model
{
    public function shipment() {
        return $this->belongsTo('App\Http\Models\Shipment', 'shipment_id', 'id');
    }
    
}
