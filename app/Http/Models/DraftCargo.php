<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DraftCargo extends Model
{
    public function draft_cargo_shipment() {
        return $this->hasMany('App\Http\Models\DraftCargoShipment');
    }
    //
    protected $fillable = [
        'origin_id','destination_id','shipments_count','cargo_type','added_by'
    ];
    public function origin() {
        return $this->belongsTo('App\Http\Models\City', 'origin_id', 'id');
    }

    public function destination() {
        return $this->belongsTo('App\Http\Models\City', 'destination_id', 'id');
    }
}
