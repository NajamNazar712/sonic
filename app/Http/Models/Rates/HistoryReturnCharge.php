<?php

namespace App\Http\Models\Rates;

use Illuminate\Database\Eloquent\Model;

class HistoryReturnCharge extends Model
{
    //
    protected $fillable = [
        'user_id','shipping_mode_id','local','national'
    ];
}
