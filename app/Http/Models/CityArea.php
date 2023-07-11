<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CityArea extends Model
{
    public function hubs()
    {
        return $this->belongsTo('App\Http\Models\City','city_id','id');
    }
}
