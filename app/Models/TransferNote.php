<?php

namespace App\Models;

use App\Http\Models\Rider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferNote extends Model
{
    use HasFactory;

    protected $table = 'transfer_note';

    public function transfer_note_shipments() {
        return $this->hasMany('App\Models\TransferNoteShipment');
    }

    public function rider()
    {
        return $this->belongsTo(Rider::class,'rider_id');
    }
}
