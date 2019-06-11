<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CityHistory extends Model
{
    protected $fillable = [
        'city_id','hub','hub_id','zone_id','pickup','status','updated_by'
    ];
}
