<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CorporateShipmentReturnDiscountCharges extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','return_discount_per'
    ];
}
