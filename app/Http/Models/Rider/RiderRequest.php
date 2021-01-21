<?php

namespace App\Http\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class RiderRequest extends Model
{
    public function city_id() {
        return $this->belongsTo('App\Http\Models\City', 'city_id', 'id');
    }
}
