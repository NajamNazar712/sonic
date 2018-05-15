<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentItem extends Model
{
    public function shipment() {
		return $this->belongsTo('App\Http\Models\Shipment');
	}

	public function product() {
		return $this->belongsTo('App\Http\Models\Product', 'product_type_id', 'id');
	}
}
