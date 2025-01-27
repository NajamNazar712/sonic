<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DonePaymentShipment extends Model
{
	public function shipment() {
		return $this->belongsTo('App\Http\Models\Shipment');
	}
    public function shipment_archive() {
		return $this->belongsTo('App\ShipmentsArchieve');
	}

    public function done_payment() {
        return $this->belongsTo('App\Http\Models\DonePayment');
    }

}
