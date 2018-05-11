<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class WeightCharge extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','range_up','range_down','weight_addition','spkg','local_or_6hr','national_or_sameday'
    ];

    public function user(){

    }
}
