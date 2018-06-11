<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PickupNoteRequest extends Model
{
	protected $primaryKey = 'pickup_note_id';

	public $timestamps = FALSE;

	public function pickup_request() {
		return $this->belongsTo('App\Http\Models\PickupRequest');
	}

	public function pickup_note() {
		return $this->belongsTo('App\Http\Models\PickupNote');
	}
}
