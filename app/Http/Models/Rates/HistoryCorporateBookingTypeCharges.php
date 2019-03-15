<?php

namespace App\Http\Models\Rates;

use Illuminate\Database\Eloquent\Model;

class HistoryCorporateBookingTypeCharges extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','replacement_charges','try_and_buy_charges'
    ];
}
