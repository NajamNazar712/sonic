<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DeliveryNoteStationDepositNote extends Model
{
    protected $table = 'delivery_note_station_deposit_notes';
    public $timestamps = false;
    protected $fillable = [ 'station_deposit_note_id','delivery_note_id' ];
}
