<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryCorporateReturnChargeZoneWise extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','local','same_zone','different_zone'
    ];
}
