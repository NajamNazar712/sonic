<?php

namespace App\Http\Models\V3Pickup;

use Illuminate\Database\Eloquent\Model;

class V3PickupNoteRequest extends Model
{
    public function pickup_note() {
        return $this->belongsTo('App\Http\Models\V3Pickup\V3PickupNote','pickup_note_id');
    }
}
