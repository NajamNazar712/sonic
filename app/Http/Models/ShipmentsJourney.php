<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentsJourney extends Model
{
	protected $table = 'shipments_journey';
	protected $fillable = [
	    'shipment_id','shipper_status_id','consignee_status_id','status_reason_id','remarks','user_id','admin_id','reference_1_id','reference_2_id'
    ];
//    public function shipment_status(){
//        return $this->belongsToMany('App\Http\Models\ShipmentStatus','');
//    }
}