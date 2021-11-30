<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceForReimbursement extends Model
{
    public function shipper() {
        return $this->belongsTo('App\Http\Models\Shipper\User', 'user_id', 'id');
    }

    public function invoice_shipments() {
        return $this->hasMany('App\Http\Models\ReimbursementInvoiceShipment','invoice_id','id');
    }
}
