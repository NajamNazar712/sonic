<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryDiscountWeightCharge extends Model
{

    public function destination()
    {
        return $this->belongsTo(City::class,'destination_id');
    }
}
