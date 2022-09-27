<?php

namespace App\Http\Models\International\Wholesale;

use Illuminate\Database\Eloquent\Model;

class WholesaleInvoiceShipment extends Model
{
    public function shipment() {
        return $this->belongsTo('App\Http\Models\International\Wholesale\WholesaleShipment', 'wholesale_shipment_id', 'id');
    }
}
