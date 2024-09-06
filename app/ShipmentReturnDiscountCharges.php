<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShipmentReturnDiscountCharges extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','return_discount_per'
    ];
}
