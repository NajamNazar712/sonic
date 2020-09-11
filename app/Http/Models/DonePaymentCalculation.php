<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DonePaymentCalculation extends Model
{
    public function done_payment() {
        return $this->belongsTo('App\Http\Models\DonePayment', 'done_payment_id', 'id');
    }
}
