<?php

namespace App\Http\Models\Admin\Attendance;

use Illuminate\Database\Eloquent\Model;

class EmployeeAttendanceActionLog extends Model
{
    protected $fillable = ['employee_id'];
    public function action_id() {
        return $this->belongsTo('App\Http\Models\AttendanceAction', 'action_id', 'id');
    }

}
