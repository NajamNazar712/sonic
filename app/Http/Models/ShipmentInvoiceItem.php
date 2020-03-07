<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentInvoiceItem extends Model
{
    public function shipment_invoice() {
        return $this->belongsTo('App\Http\Models\ShipmentInvoice');
    }
}
