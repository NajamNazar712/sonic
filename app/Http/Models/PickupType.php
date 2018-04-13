<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PickupType extends Model
{
    public function cities(){
        return $this->belongsToMany('App\Http\Models\CityInfo','city_pickup','pickup_type_id','city_code');
    }
}
