<?php

namespace App\Http\Models\Excel_reports;

use Illuminate\Database\Eloquent\Model;

class Debriefing extends Model
{
    public function city() {
    return $this->belongsTo('App\Http\Models\City', 'hub');
    }
}
