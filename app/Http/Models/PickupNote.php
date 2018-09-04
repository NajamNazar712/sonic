<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PickupNote extends Model
{
	public function pickup_note_requests() {
		return $this->hasMany('App\Http\Models\PickupNoteRequest');
	}

	public function rider() {
        return $this->belongsTo('App\Http\Models\Rider');
    }
}