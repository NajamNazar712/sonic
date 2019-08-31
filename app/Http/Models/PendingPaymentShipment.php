<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PendingPaymentShipment extends Model
{
	public function shipment() {
		return $this->belongsTo('App\Http\Models\Shipment');
	}

	public function pending_payment() {
		return $this->belongsTo('App\Http\Models\PendingPayment');
	}
}
