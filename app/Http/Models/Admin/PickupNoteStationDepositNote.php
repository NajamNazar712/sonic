<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class PickupNoteStationDepositNote extends Model
{
    protected $table = 'pickup_note_station_deposit_notes';
    protected $fillable = [ 'station_deposit_note_id','retail_pickup_note_id' ];
}
