<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CityDelivery extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = null;
    protected $fillable = [
        'city_id','booking_type_id','shipping_mode_id'
    ];
}
