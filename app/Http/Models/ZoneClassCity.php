<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneClassCity extends Model
{
    public function city()
    {
        return $this->belongsTo('App\Http\Models\City', 'city_id');
    }
    public function zone_classification()
    {
        return $this->belongsTo('App\zone_classification', 'zone_classification_id');
    }
}
