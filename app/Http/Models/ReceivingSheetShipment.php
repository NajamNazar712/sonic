<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivingSheetShipment extends Model
{
    protected $primaryKey = 'shipment_id';
    public $timestamps = FALSE;

    public function receiving_sheet() {
		return $this->belongsTo('App\Http\Models\ReceivingSheet');
	}

	public function shipment() {
		return $this->belongsTo('App\Http\Models\Shipment');
	}
}
