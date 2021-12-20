<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsPickupRun extends Model
{
    public function pickup_run_requests() {
		return $this->hasMany('App\Http\Models\WMS\WmsPickupRunRequest', 'pickup_run_id', 'id');
	}
	public function rider(){
        return $this->belongsTo('App\Http\Models\Rider', 'wms_rider_id', 'id')->withTrashed();
    }
}
