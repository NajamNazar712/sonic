<?php

namespace App\Http\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class RiderReturnNoteStatus extends Model
{
    public function return_note_id() {
        return $this->belongsTo('App\Http\Models\Admin\ReturnNote', 'return_note_id', 'id');
    }

}
