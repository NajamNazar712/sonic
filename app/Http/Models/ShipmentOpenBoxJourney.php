<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentOpenBoxJourney extends Model
{
    public function admin() {
		return $this->belongsTo('App\Http\Models\Admin\Admin', 'created_by', 'id');
	}
	
	public function shipment_open_status() {
    	return $this->belongsTo('App\Http\Models\ShipmentOpenBoxStatus', 'open_box_status_id', 'id');
    }
}
