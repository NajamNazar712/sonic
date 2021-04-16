<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateDefaultCashHandlingCharge extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','range_up','range_down','charges'
    ];
}
