<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceShipment extends Model
{
	public function shipment() {
		return $this->belongsTo('App\Http\Models\Shipment');
	}
}
