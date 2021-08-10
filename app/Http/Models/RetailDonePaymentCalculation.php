<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RetailDonePaymentCalculation extends Model
{
    public function retail_done_payment() {
        return $this->belongsTo('App\Http\Models\RetailDonePayment', 'retail_done_payment_id', 'id');
    }
}
