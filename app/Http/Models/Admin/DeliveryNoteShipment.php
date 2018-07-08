<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DeliveryNoteShipment extends Model
{
    public $timestamps = FALSE;
    protected $fillable = [
        'delivery_note_id','shipment_id'
    ];
}
