<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentsJourney extends Model
{
	protected $table = 'shipments_journey';
	protected $fillable = [
	    'shipment_id','shipper_status_id','consignee_status_id','status_reason_id','remarks','user_id','admin_id','reference_1_id','reference_2_id'
    ];

    public function shipment_status_shipper() {
    	return $this->belongsTo('App\Http\Models\ShipmentStatus', 'shipper_status_id', 'id');
    }

    public function shipment_status_consignee() {
    	return $this->belongsTo('App\Http\Models\ShipmentStatus', 'consignee_status_id', 'id');
    }

    public function shipment_status_reason() {
    	return $this->belongsTo('App\Http\Models\ShipmentStatusReason');
    }

    public function admin() {
    	return $this->belongsTo('App\Http\Models\Admin\Admin', 'admin_id', 'id');
    }

    public function user() {
    	return $this->belongsTo('App\Http\Models\Shipper\User', 'user_id', 'id');
    }
}