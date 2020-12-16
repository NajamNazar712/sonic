<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsPickupRunRequestProduct extends Model
{
    public function product(){
        return $this->belongsTo('App\Http\Models\WMS\WmsProduct');
    }
}
