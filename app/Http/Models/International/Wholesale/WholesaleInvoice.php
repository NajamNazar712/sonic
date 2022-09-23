<?php

namespace App\Http\Models\International\Wholesale;

use Illuminate\Database\Eloquent\Model;

class WholesaleInvoice extends Model
{
    public function shipper() {
        return $this->belongsTo('App\Http\Models\International\Wholesale\WholesaleUser', 'wholesale_user_id', 'id');
    }

    public function invoice_shipments() {
        return $this->hasMany('App\Http\Models\International\Wholesale\WholesaleInvoiceShipment');
    }
}
