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
}
