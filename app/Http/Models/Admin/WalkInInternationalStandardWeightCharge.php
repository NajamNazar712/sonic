<?php

namespace App\Http\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class WalkInInternationalStandardWeightCharge extends Model
{
    public function hubs() {
        return $this->hasMany('App\Http\Models\Admin\WalkInInternationalStandardWeightChargeHub', 'international_charges_id', 'id');
    }
}
