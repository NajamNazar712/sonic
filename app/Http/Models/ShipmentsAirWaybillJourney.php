<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentsAirWaybillJourney extends Model
{
	protected $table = 'shipments_air_waybill_journey';
	protected $fillable = ['shipment_id', 'user_type', 'user_id'];
}