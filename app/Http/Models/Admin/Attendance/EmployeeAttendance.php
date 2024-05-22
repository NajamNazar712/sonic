<?php

namespace App\Http\Models\Admin\Attendance;

use Illuminate\Database\Eloquent\Model;

class EmployeeAttendance extends Model
{
    protected $fillable = ['employee_id', 'link_id', 'clock_in', 'clock_out'];
}
