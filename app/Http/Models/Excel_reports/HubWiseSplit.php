<?php

namespace App\Http\Models\Excel_reports;

use Illuminate\Database\Eloquent\Model;

class HubWiseSplit extends Model
{
    public function origin_city() {
        return $this->belongsTo('App\Http\Models\City', 'origin', 'id');
    }
    public function city() {
        return $this->belongsTo('App\Http\Models\City', 'hub_id', 'id');
    }
}
