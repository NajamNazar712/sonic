<?php

namespace App\Http\Models\Rates;

use Illuminate\Database\Eloquent\Model;

class CorporateRateHistory extends Model
{
    //
    protected $fillable = [
        'user_id','rate_id','updated_by','approved_by','from_date','to_date'
    ];
}
