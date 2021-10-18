<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RunnerDetailTime extends Model
{
    public function origin_hub() {
        return $this->belongsTo('App\Http\Models\City', 'origin', 'id');
    }
    public function destination_hub() {
        return $this->belongsTo('App\Http\Models\City', 'destination', 'id');
    }
}
