<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivingSheet extends Model
{
	public function receiving_sheet_shipments() {
		return $this->hasMany('App\Http\Models\ReceivingSheetShipment');
	}
}
