<?php

namespace App\Http\Models\V2Pickup;

use Illuminate\Database\Eloquent\Model;

class V2PickupNoteRequest extends Model
{
    public function pickup_request() {
		return $this->belongsTo('App\Http\Models\V2Pickup\V2PickupRequest');
	}

	public function pickup_note() {
		return $this->belongsTo('App\Http\Models\V2Pickup\V2PickupNote');
	}
}
