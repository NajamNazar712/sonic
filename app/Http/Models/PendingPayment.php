<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PendingPayment extends Model
{
	public function pending_payment_shipments() {
		return $this->hasMany('App\Http\Models\PendingPaymentShipment');
	}
}
