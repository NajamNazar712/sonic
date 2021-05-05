<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateDefaultFuelSurcharge extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','fuel_surcharge'
    ];
}
