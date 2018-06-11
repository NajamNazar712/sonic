<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class FuelSurcharge extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','fuel_surcharge'
    ];
}
