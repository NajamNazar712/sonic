<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentStatusReason extends Model
{
    protected $table = 'shipment_status_reason';

    public function status(){
        return $this->belongsToMany('App\Http\Models\ShipmentStatus');
    }
}
