<?php

namespace App\Http\Models\Admin\Attendance;

use Illuminate\Database\Eloquent\Model;

class AdminAttendanceActionLog extends Model
{
    public function admin_id() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'admin_id', 'id');
    }
    public function action_id() {
        return $this->belongsTo('App\Http\Models\AttendanceAction', 'action_id', 'id');
    }

}
