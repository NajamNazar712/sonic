<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CityDelivery extends Model
{
    protected $fillable = [
        'city_id','booking_type_id','shipping_mode_id'
    ];
}
