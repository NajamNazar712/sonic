<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RetailStandardRates extends Model
{
    protected $fillable = ['range_up','range_down','shipping_mode_id','kg_range','zone_a','zone_b','zone_c','zone_d','within_city','same_zone','different_zone','weight_addition'];
}
