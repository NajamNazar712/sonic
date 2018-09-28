<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CargoConsignmentShipment extends Model
{
	public $timestamps = FALSE;

	public function shipment() {
		return $this->belongsTo('App\Http\Models\Shipment');
	}
}