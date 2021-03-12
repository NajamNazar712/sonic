<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RetailDonePayment extends Model
{
    public function done_payment_shipments() {
        return $this->hasMany('App\Http\Models\RetailDonePaymentShipment');
    }

    public function shipper() {
        return $this->belongsTo('App\http\Models\Admin\Retail\RetailShipperInfo', 'user_id', 'id');
    }
    public function company_bank() {
        return $this->belongsTo('App\Http\Models\BanksList', 'company_bank_id', 'id');
    }

}
