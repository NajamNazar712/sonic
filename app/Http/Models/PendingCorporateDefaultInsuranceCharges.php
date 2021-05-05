<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PendingCorporateDefaultInsuranceCharges extends Model
{
    protected $fillable = [
        'user_id','shipping_mode_id','range_up','range_down','charges'
    ];
}
