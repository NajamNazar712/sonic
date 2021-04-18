<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateDefaultRateHistory extends Model
{
    protected $fillable = [
        'user_id','rate_id','updated_by','approved_by','from_date','to_date'
    ];
}
