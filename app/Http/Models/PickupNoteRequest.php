<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PickupNoteRequest extends Model
{
    public $timestamps = FALSE;

    public function pickup_request() {
		return $this->belongsTo('App\Http\Models\PickupRequest');
	}
}
