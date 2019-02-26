<?php

namespace App\Http\Models\Rates;

use Illuminate\Database\Eloquent\Model;

class PendingCorporateReturnCharge extends Model
{
    //
    protected $fillable = [
        'user_id','shipping_mode_id','local','national'
    ];
}
