<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentsPickupJourney extends Model
{
	protected $table = 'shipments_pickup_journey';
	protected $fillable = ['shipment_id', 'status_id', 'admin_id'];

    public function status() {
    	return $this->belongsTo('App\Http\Models\ShipmentPickupStatus', 'status_id', 'id');
    }

    public function admin() {
    	return $this->belongsTo('App\Http\Models\Admin\Admin', 'admin_id', 'id');
    }
}