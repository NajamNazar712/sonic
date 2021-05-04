<?php

namespace App\Http\Models\V2Pickup;

use Illuminate\Database\Eloquent\Model;

class V2PickupNote extends Model
{
    public function pickup_note_requests() {
        return $this->hasMany('App\Http\Models\V2Pickup\V2PickupNoteRequest','pickup_note_id');
    }

    public function pickup_note_requests_picked() {
        return $this->hasMany('App\Http\Models\V2Pickup\V2PickupNoteRequest','pickup_note_id')->where('
        status', 1)->get();
    }

    public function rider() {
        return $this->belongsTo('App\Http\Models\Rider');
    }
    public function city() {
        return $this->belongsTo('App\Http\Models\City');
    }
}
