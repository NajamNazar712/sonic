<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderTransferNoteRequestShipment extends Model
{
    use HasFactory;

    public function rider_transfer_note()
    {
        $this->belongsTo(RiderTransferNoteRequest::class,'request_note_id');
    }
}
