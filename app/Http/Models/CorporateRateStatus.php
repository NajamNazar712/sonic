<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateRateStatus extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','status','cash_handling_charges','insurance_charges','fuel_charges','return_charges','packaging_charges'
    ];
}
