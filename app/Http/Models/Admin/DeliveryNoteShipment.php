<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DeliveryNoteShipment extends Model
{
    protected $primaryKey = 'shipment_id';
    public $timestamps = FALSE;
    protected $fillable = [
        'delivery_note_id','shipment_id'
    ];
}
