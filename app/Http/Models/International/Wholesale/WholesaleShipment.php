<?php

namespace App\Http\Models\International\Wholesale;

use Illuminate\Database\Eloquent\Model;

class WholesaleShipment extends Model
{
    public function destination_city() {
        return $this->belongsTo('App\Http\Models\City', 'destination_city_id', 'id');
    }
}
