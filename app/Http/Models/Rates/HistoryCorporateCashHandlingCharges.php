<?php

namespace App\Http\Models\Rates;

use Illuminate\Database\Eloquent\Model;

class HistoryCorporateCashHandlingCharges extends Model
{
    //
    protected $fillable = [
        'user_id','shipping_mode_id','range_up','range_down','charges'
    ];
}
