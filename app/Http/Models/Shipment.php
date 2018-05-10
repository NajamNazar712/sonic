<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
	public function items() {
		return $this->hasMany('ShipmentItem');
	}
}