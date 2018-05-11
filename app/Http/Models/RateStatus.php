<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RateStatus extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','status','cash_handling_charges','insurance_charges','return_charges','packaging_charges'
    ];
}
