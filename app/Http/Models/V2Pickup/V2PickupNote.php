<?php

namespace App\Http\Models\V2Pickup;

use Illuminate\Database\Eloquent\Model;

class V2PickupNote extends Model
{
    public function pickup_note_requests() {
        return $this->hasMany('App\Http\Models\V2Pickup\V2PickupNoteRequest','pickup_note_id');
    }

    public function rider() {
        return $this->belongsTo('App\Http\Models\Rider');
    }
}
