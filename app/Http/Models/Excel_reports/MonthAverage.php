<?php

namespace App\Http\Models\Excel_reports;

use Illuminate\Database\Eloquent\Model;

class MonthAverage extends Model
{
    public function city() {
        return $this->belongsTo('App\Http\Models\City', 'origin_id', 'id');
    }
}
