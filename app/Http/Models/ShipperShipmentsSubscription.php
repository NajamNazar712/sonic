<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipperShipmentsSubscription extends Model
{
    public function shipper() {
        return $this->belongsTo('App\Http\Models\Shipper\User', 'shipper_id', 'id');
    }
    public function shipment() {
        return $this->belongsTo('App\Http\Models\Shipment', 'shipment_id', 'id');
    }
}
