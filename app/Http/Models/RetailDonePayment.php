<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RetailDonePayment extends Model
{
    public function done_payment_shipments() {
        return $this->hasMany('App\Http\Models\RetailDonePaymentShipment');
    }
}
