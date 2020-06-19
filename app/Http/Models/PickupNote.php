<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PickupNote extends Model
{
	public function pickup_note_requests() {
		return $this->hasMany('App\Http\Models\PickupNoteRequest', 'pickup_note_id');
	}

	public function rider() {
        return $this->belongsTo('App\Http\Models\Rider');
    }

    public function city() {
        return $this->belongsTo('App\Http\Models\City');
    }
}