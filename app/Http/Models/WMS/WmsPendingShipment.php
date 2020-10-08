<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsPendingShipment extends Model
{
    public function shipment_products() {
        return $this->hasMany('App\Http\Models\WMS\WmsPendingShipmentProduct', 'pending_shipment_id', 'id');
    }
    public function shipment_packaging() {
        return $this->hasMany('App\Http\Models\WMS\WmsPendingShipmentPackaging', 'pending_shipment_id', 'id');
    }
    public function shipment_replacement_products() {
        return $this->hasMany('App\Http\Models\WMS\WmsPendingShipmentReplacementProduct', 'pending_shipment_id', 'id');
    }
}
