<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempDeliveryNoteVerify extends Model
{
    use HasFactory;

    protected $fillable = [
        'rider_id', 'request_date', 'shipment_set_hash', 'shipment_ids_csv',
        // 'created_note_id', // if you added it
    ];
}
