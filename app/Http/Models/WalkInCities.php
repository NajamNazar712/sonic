<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class WalkInCities extends Model
{
    //
    public $timestamps = FALSE;

    protected $fillable = [
        'city_id','pickup','delivery'
        ];
}
