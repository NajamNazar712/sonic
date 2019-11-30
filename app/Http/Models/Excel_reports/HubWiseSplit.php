<?php

namespace App\Http\Models\Excel_reports;

use Illuminate\Database\Eloquent\Model;

class HubWiseSplit extends Model
{
    public function city() {
        return $this->belongsTo('App\Http\Models\City', 'hub_id', 'id');
    }
}
