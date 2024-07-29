<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RetailUserHistory extends Model
{
    protected $fillable = [
        'retail_user_id',
        'trax_center_id',
        'trax_center_name',
        'trax_center_code',
        'joining_date',
        'last_date',
        'retail_shipping_mode_id',
        'retail_shipping_mode_name',
        'product_commission',
        'booking_date',
    ];
}
