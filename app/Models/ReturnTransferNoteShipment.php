<?php

namespace App\Models;

use App\Http\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnTransferNoteShipment extends Model
{
    use HasFactory;

    public function shipment()
    {
        return $this->belongsTo(Shipment::class,'shipment_id');
    }
}
