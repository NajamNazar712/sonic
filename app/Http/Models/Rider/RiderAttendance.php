<?php

namespace App\Http\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class RiderAttendance extends Model
{
    public function rider_id() {
        return $this->belongsTo('App\Http\Models\Rider', 'rider_id', 'id');
    }
}
