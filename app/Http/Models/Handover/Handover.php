<?php

namespace App\Http\Models\Handover;

use Illuminate\Database\Eloquent\Model;

class Handover extends Model
{
    public function status(){
        return $this->hasOne('App\Http\Models\Handover\HandoverStatus', 'id', 'status_id');
    }
    public function handover_note_shipments() {
        return $this->hasMany('App\Http\Models\Handover\HandoverShipments');
    }
}
