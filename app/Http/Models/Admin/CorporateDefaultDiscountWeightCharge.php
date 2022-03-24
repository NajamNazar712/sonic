<?php

namespace App\Http\Models\Admin;

use App\Http\Models\City;
use Illuminate\Database\Eloquent\Model;

class CorporateDefaultDiscountWeightCharge extends Model
{
    protected $fillable = ['user_id','shipping_mode_id','destination_id','range_up','range_down','weight_addition','spkg','local_or_6hr'];

    public function destination()
    {
        return $this->belongsTo(City::class,'destination_id');
    }
}
