<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsStoreRequestProducts extends Model
{
   	public function product() {
		return $this->belongsTo('App\Http\Models\WMS\WmsProduct', 'product_id', 'id');
	}
}
