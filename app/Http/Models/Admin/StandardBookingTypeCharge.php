<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class StandardBookingTypeCharge extends Model
{
    protected $fillable = [
        'shipping_mode_id', 'replacement_charges', 'try_and_buy_charges',
    ];
}
