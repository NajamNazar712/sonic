<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CityHistory extends Model
{
    protected $fillable = [
        'city_id','hub','hub_id','zone_id','pickup','status','updated_by','gc_area','attempt_tat','location_latitude','location_longitude','address','hub_location_latitude','hub_location_longitude','pickup_cut_off_time'
    ];
}
