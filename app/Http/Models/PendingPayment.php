<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PendingPayment extends Model
{
	public function pending_payment_shipments() {
		return $this->hasMany('App\Http\Models\PendingPaymentShipment');
	}

    public function pending_payment_calculation(){
        return $this->hasOne('App\Http\Models\PendingPaymentCalculation');
    }

	public function shipper() {
		return $this->belongsTo('App\Http\Models\Shipper\User', 'user_id', 'id');
	}
}
