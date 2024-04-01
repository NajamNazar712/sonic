<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CityArea extends Model
{
    public function hubs()
    {
        return $this->belongsTo('App\Http\Models\City','city_id','id');
    }

    public function reporting_location()
    {
        return $this->belongsTo('App\Http\Models\ReportingLocation','report_location_id','id');
    }

    public function responsible_admins(){
        return $this->hasMany('App\Http\Models\Admin\Admin','default_hub_id','id');
    }
}
