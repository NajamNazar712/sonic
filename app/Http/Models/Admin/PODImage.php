<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PODImage extends Model
{
    public function shipment() {
		return $this->belongsTo('App\Http\Models\Shipment');
	}
}
