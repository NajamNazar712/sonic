<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RiderDelivery extends Model
{
    public function rider() {
        return $this->belongsTo('App\Http\Models\Rider','rider_id','id')->groupBy('id');
    }
}
