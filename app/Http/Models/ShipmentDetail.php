<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentDetail extends Model
{
    protected $fillable = ['shipment_id', 'is_open'];
    public function shipment() {
		return $this->belongsTo('App\Http\Models\Shipment');
	}
}
