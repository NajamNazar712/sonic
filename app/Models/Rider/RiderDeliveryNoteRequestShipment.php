<?php

namespace App\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class RiderDeliveryNoteRequestShipment extends Model
{
    protected $fillable=[
        'request_note_id','shipment_id','notification','rider_information','open_box','ordering'];
}
