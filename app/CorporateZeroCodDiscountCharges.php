<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CorporateZeroCodDiscountCharges extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','cod_discount_per'
    ];
}
