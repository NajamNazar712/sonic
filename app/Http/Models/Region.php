<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public function zone_region() {
        return $this->hasMany('App\Http\Models\ZoneRegion');
    }
}
