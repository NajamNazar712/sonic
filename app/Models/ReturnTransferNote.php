<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnTransferNote extends Model
{
    use HasFactory;

    public function return_note_shipments()
    {
     return $this->belongsTo(ReturnTransferNoteShipment::class,'return_note_id');
    }
}
