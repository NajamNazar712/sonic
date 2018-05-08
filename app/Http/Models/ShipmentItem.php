<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentItem extends Model
{
    public function shipment() {
		return $this->belongsTo('Shipment');
	}
}
