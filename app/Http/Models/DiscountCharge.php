<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCharge extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','weight','cash','insurance','return','packaging','to','from'
    ];
}
