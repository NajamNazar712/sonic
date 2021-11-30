<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PendingDwsWeightCharges extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','dws_weight_status','admin_id'
    ];
    
}
