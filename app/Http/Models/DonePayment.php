<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DonePayment extends Model
{
	public function done_payment_shipments() {
		return $this->hasMany('App\Http\Models\DonePaymentShipment');
	}

	public function shipper() {
		return $this->belongsTo('App\Http\Models\Shipper\User', 'user_id', 'id');
	}

	public function company_bank() {
		return $this->belongsTo('App\Http\Models\BanksList', 'company_bank_id', 'id');
	}
}
