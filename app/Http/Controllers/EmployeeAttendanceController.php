<?php

namespace App\Http\Controllers;

use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\Attendance\EmployeeAttendanceActionLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeAttendanceController extends Controller
{
    /*public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('Permission');
    }*/

    static public function riders_attendance_mark($rider_id){
        $attendance_datetime = Carbon::now()->format('Y-m-d H:i:s');
        $attendance_date = Carbon::now()->format('Y-m-d');


        $rider_attendance = EmployeeAttendance::where('employee_id', $rider_id)
            ->whereDate('attendance_date', $attendance_date)
            ->where('employee_type', 2);
        if (!$rider_attendance->exists()) {
            $rider_attendance = new EmployeeAttendance();
            $rider_attendance->employee_id = $rider_id;
            $rider_attendance->employee_type = 2;
            $rider_attendance->attendance_date = $attendance_date;
            $rider_attendance->clock_in_datetime = $attendance_datetime;
            $rider_attendance->clock_in_latitude = '0';
            $rider_attendance->clock_in_longitude = '0';
            $rider_attendance->save();

            $rider_attendance_action = new EmployeeAttendanceActionLog();
            $rider_attendance_action->employee_id = $rider_id;
            $rider_attendance_action->employee_type = 2;
            $rider_attendance_action->action_id = 1;
            $rider_attendance_action->attendance_date = $attendance_date;
            $rider_attendance_action->action_date = $attendance_datetime;
            $rider_attendance_action->latitude = '0';
            $rider_attendance_action->longitude = '0';
            $rider_attendance_action->save();
        }else{
            $rider_attendance = $rider_attendance->get()->first();
            if($rider_attendance->clock_in_datetime == NULL){
                $rider_attendance->clock_in_datetime = $attendance_datetime;
                $rider_attendance->save();

                $rider_attendance_action = new EmployeeAttendanceActionLog();
                $rider_attendance_action->employee_id = $rider_id;
                $rider_attendance_action->employee_type = 2;
                $rider_attendance_action->action_id = 1;
                $rider_attendance_action->attendance_date = $attendance_date;
                $rider_attendance_action->action_date = $attendance_datetime;
                $rider_attendance_action->latitude = '0';
                $rider_attendance_action->longitude = '0';
                $rider_attendance_action->save();
            }

        }
    }
}
