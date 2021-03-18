<?php

namespace App\Http\Models\Admin\Attendance;

use Illuminate\Database\Eloquent\Model;

class AdminAttendance extends Model
{
    public function admin_id() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'admin_id', 'id');
    }
}
