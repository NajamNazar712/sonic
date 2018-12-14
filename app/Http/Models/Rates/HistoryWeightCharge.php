<?php

namespace App\Http\Models\Rates;

use Illuminate\Database\Eloquent\Model;

class HistoryWeightCharge extends Model
{
    //
    protected $fillable = [
        'user_id','shipping_mode_id','range_up','range_down','weight_addition','spkg','local_or_6hr','national_charges_class_0','national_charges_class_1','national_charges_class_2','national_charges_class_3'
    ];
}
