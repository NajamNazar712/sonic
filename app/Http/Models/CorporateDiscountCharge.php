<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateDiscountCharge extends Model
{

    protected $fillable = [
        'user_id','shipping_mode_id','title','weight','cash','insurance','return','to','from','added_by'
    ];
}
