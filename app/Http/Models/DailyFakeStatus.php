<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DailyFakeStatus extends Model
{
    public function rider(){
        return $this->belongsTo('App\Http\Models\Rider');
    }
    public function hub(){
        return $this->belongsTo('App\Http\Models\City');
    }
    public function zone() {
        return $this->belongsTo('App\Http\Models\Zone', 'zone_id');
    }
}
