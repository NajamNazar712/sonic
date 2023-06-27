<?php

namespace App\Http\Models\Handover;

use Illuminate\Database\Eloquent\Model;

class HandoverResponsibilities extends Model
{
    public function city_area()
    {
        return $this->belongsTo('App\Http\Models\CityArea','city_area_id','id');
    }
}
