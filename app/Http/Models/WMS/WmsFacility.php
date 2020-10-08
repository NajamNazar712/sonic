<?php

namespace App\Http\Models\WMS;

use Illuminate\Database\Eloquent\Model;

class WmsFacility extends Model
{
    public function city(){
        return $this->belongsTo('App\Http\Models\City');
    }
}
