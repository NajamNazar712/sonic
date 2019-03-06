<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateFuelSurcharge extends Model
{
    protected $table = 'corporate_fuel_surcharges';
    protected $fillable = [
        'user_id','shipping_mode_id','fuel_surcharge'
    ];
}
