<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class InternationalShipmentsLog extends Model
{
    public function international_shipment(){
        return $this->belongsTo('App\Http\Models\InternationalShipment', 'shipment_id', 'shipment_id');
    }
    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'updated_by', 'id');
    }
}
