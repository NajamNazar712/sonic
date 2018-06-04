<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CityHub extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = null;
    protected $fillable = [
        'city_id','hub_id'
    ];
}
