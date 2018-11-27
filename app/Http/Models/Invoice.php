<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
	public function invoice_shipments() {
		return $this->hasMany('App\Http\Models\InvoiceShipment');
	}

	public function shipper() {
		return $this->belongsTo('App\Http\Models\Shipper\User', 'user_id', 'id');
	}
}
