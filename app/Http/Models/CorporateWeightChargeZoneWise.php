<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateWeightChargeZoneWise extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','delivery_type_id','range_up','range_down','base','local','same_zone','different_zone'
    ];
}
