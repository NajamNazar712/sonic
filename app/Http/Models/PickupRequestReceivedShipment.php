<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PickupRequestReceivedShipment extends Model
{
	protected $primaryKey = 'shipment_id';
	public $incrementing = FALSE;
	public $timestamps = FALSE;
}