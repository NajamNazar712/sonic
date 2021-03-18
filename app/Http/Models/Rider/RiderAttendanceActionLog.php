<?php

namespace App\Http\Models\Rider;

use Illuminate\Database\Eloquent\Model;

class RiderAttendanceActionLog extends Model
{

    public function rider_id() {
        return $this->belongsTo('App\Http\Models\Rider', 'rider_id', 'id');
    }
    public function action_id() {
        return $this->belongsTo('App\Http\Models\AttendanceAction', 'action_id', 'id');
    }
}
