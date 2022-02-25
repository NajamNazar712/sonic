<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsCourierOrders extends Model
{
    public function user() {
        return $this->belongsTo('App\Http\Models\Shipper\User', 'shipper_id');
    }

    public function wms_order_packing() {
        return $this->hasMany('App\Http\Models\WMS\WmsOrderPackaging', 'shipment_id');
    }

    public function order_processes(){
        return $this->hasOne('App\Http\Models\WMS\WmsOrderProcess', 'shipment_id');
    }
}
