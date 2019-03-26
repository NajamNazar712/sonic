<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    public function zone_cities() {
        return $this->hasMany('App\Http\Models\City');
    }

    public function zone_class_cities() {
        return $this->hasMany('App\Http\Models\ZoneClassCity');
    }
}
