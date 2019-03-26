<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class WalkInStandardWeightCharge extends Model
{
    //
    public $timestamps = FALSE;
    protected $fillable = [
        'shipping_mode_id', 'delivery_type_id', 'actual_weight', 'chargeable_weight', 'local', 'national'
    ];
}
