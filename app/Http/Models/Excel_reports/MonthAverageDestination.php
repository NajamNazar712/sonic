<?php

namespace App\Http\Models\Excel_reports;

use Illuminate\Database\Eloquent\Model;

class MonthAverageDestination extends Model
{
    //
    public function city() {
        return $this->belongsTo('App\Http\Models\City', 'destination_id', 'id');
    }
}
