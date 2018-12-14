<?php

namespace App\Http\Models\Rates;

use Illuminate\Database\Eloquent\Model;

class PendingDiscountCharge extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','title','weight','cash','insurance','return','packaging','to','from','added_by'
    ];
}
