<?php

namespace App\Models;

use App\Http\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferNoteShipment extends Model
{
    use HasFactory;
    protected $table = 'transfer_note_shipments';

    public function shipment()
    {
        return $this->belongsTo(Shipment::class,'shipment_id');
    }

    public function transfer_note()
    {
        return $this->belongsTo(TransferNote::class,'transfer_note_id');
    }
}
