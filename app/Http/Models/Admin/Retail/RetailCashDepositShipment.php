<?php

namespace App\Http\Models\Admin\Retail;

use Illuminate\Database\Eloquent\Model;

class RetailCashDepositShipment extends Model
{
    public function shipment() {
        return $this->belongsTo('App\Http\Models\Shipment', 'shipment_id', 'id');
    }
    public function shipping_mode() {
        return $this->belongsTo('App\Http\Models\Admin\Retail\RetailShippingMode', 'shipping_mode_id', 'id');
    }

    public function retail_shipment() {
        return $this->belongsTo('App\Http\Models\Admin\Retail\RetailShipment', 'shipment_id', 'shipment_id');
    }
}
