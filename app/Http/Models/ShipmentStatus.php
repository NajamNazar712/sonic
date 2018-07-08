<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentStatus extends Model
{
    protected $table = 'shipment_status';

    public function reasons(){
        return $this->belongsToMany('App\Http\Models\ShipmentStatusReason');
    }
}
