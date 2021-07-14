<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentDistributionProduct extends Model
{
    public function item()
    {
        return $this->belongsTo('App\Http\Models\DistributionProduct','product_type_id');
    }
}
