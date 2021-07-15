<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentDistributionProduct extends Model
{
    public function shipment() {
        return $this->belongsTo('App\Http\Models\Shipment');
    }
    public function product() {
        return $this->belongsTo('App\Http\Models\DistributionProduct', 'product_type_id', 'id');
    }
}
