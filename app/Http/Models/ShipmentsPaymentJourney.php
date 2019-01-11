<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentsPaymentJourney extends Model
{
	protected $table = 'shipments_payment_journey';
	protected $fillable = ['shipment_id', 'status_id', 'admin_id', 'reference_1_id', 'reference_2_id'];

    public function status() {
    	return $this->belongsTo('App\Http\Models\ShipmentPaymentStatus', 'status_id', 'id');
    }

    public function admin() {
    	return $this->belongsTo('App\Http\Models\Admin\Admin', 'admin_id', 'id');
    }
}