<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class WalkInStandardWeightCharge extends Model
{
    //
    public $timestamps = FALSE;
    protected $fillable = [
        'actual_weight','chargeable_weight'
    ];
}
