<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = [
        'name',
        'gst',
        'status',
        'business_category_id',
    ];
    public function zone_cities() {
        return $this->hasMany('App\Http\Models\City');
    }

    public function zone_class_cities() {
        return $this->hasMany('App\Http\Models\ZoneClassCity');
    }

    public function zone_region() {
        return $this->hasOne('App\Http\Models\ZoneRegion');
    }
}
