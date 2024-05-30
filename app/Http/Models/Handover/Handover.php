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

    public function to_admin()
    {
        return $this->belongsTo('App\Http\Models\Admin\Admin','to','id');
    }

    public function from_admin()
    {
        return $this->belongsTo('App\Http\Models\Admin\Admin','from','id');
    }
}
