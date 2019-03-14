<?php

namespace App\Http\Models\Rates;

use Illuminate\Database\Eloquent\Model;

class HistoryCorporateReturnCharge extends Model
{
    //
    protected $fillable = [
        'user_id','shipping_mode_id','local','national_charges_class_0','national_charges_class_1','national_charges_class_2','national_charges_class_3'
    ];
}
