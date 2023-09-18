<?php

namespace App\Http\Models\V3Pickup;

use Illuminate\Database\Eloquent\Model;

class V3PickupNote extends Model
{
    public function pickup_note_requests() {
        return $this->hasMany('App\Http\Models\V3Pickup\V3PickupNoteRequest','pickup_note_id');
    }
    public function rider() {
        return $this->belongsTo('App\Http\Models\Rider');
    }
    public function pickup_note_requests_picked() {
        return $this->hasMany('App\Http\Models\V3Pickup\V3PickupNoteRequest','pickup_note_id')->where('
        status', 1)->get();
    }
}
