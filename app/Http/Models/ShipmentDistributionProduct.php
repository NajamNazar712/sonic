<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentDistributionProduct extends Model
{
    public function shipment() {
        return $this->belongsTo('App\Http\Models\Shipment');
    }
    public function item()
    {
        return $this->belongsTo('App\Http\Models\DistributionProduct','product_type_id');
    }


}
