<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentInvoice extends Model
{
    public function items() {
        return $this->hasMany('App\Http\Models\ShipmentInvoiceItem', 'shipment_invoice_id');
    }
}
