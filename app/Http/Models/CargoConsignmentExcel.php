<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CargoConsignmentExcel extends Model
{
    public function cargo_consignment_shipments() {
        return $this->hasMany('App\Http\Models\CargoConsignmentShipmentExcel')->orderBy('shipment_id');
    }
}
