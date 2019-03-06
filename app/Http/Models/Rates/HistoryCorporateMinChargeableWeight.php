<?php

namespace App\Http\Models\Rates;

use Illuminate\Database\Eloquent\Model;

class HistoryCorporateMinChargeableWeight extends Model
{
    //
    protected $fillable = [
        'user_id','shipping_mode_id','delivery_type_id','min_chargeable_weight'
    ];
}
