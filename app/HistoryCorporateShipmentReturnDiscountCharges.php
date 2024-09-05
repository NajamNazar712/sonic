<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryCorporateShipmentReturnDiscountCharges extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','return_discount_per'
    ];
}
