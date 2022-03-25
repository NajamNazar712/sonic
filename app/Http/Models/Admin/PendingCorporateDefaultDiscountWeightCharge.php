<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PendingCorporateDefaultDiscountWeightCharge extends Model
{
    protected $fillable = ['user_id','shipping_mode_id','destination_id','range_up','range_down','weight_addition','spkg','local_or_6hr'];
}
