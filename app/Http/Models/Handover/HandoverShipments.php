<?php

namespace App\Http\Models\Handover;

use Illuminate\Database\Eloquent\Model;

class HandoverShipments extends Model
{
    public function handover_id_latest(){
        return $this->hasOne('App\Http\Models\Handover\Handover', 'handover_id')->latest('id');
    }
}
