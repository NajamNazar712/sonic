<?php

use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\Attendance\EmployeeAttendanceActionLog;
use App\Http\Models\Rider;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RiderAttendanceDeliveryNoteNovember extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $from = Carbon::createFromFormat('Y-m-d', '2022-10-21');
        $to = Carbon::createFromFormat('Y-m-d', '2022-11-23');

        $length = $from->diffInDays($to);

        $dates = [];
        $all_dates = array();
        while ($from->lte($to)){
            $all_dates[] = $from->toDateString();

            $from->addDay();
        }
        $details = [];
        foreach($all_dates as $date){
            $riders = array();
            $delivery_notes = \App\Http\Models\Admin\DeliveryNote::whereDate('created_at', $date);
            if($delivery_notes->exists()){
                $delivery_notes = $delivery_notes->get();
                foreach ($delivery_notes as $delivery_note){
                    if(!in_array($delivery_note->rider_id, $riders)){
                        $attendance_datetime = Carbon::parse($delivery_note->created_at)->format('Y-m-d H:i:s');
                        $attendance_date = $date;

                        $employee_id = Rider::find($delivery_note->rider_id)->employee_id;
                        if($employee_id != null){
                            $rider_attendance = EmployeeAttendance::where('employee_id', $employee_id)
                                ->whereDate('attendance_date', $attendance_date)
                                ->where('employee_type', 2);
                            if (!$rider_attendance->exists()) {
                                $rider_attendance = new EmployeeAttendance();
                                $rider_attendance->employee_id = $employee_id;
                                $rider_attendance->employee_type = 2;
                                $rider_attendance->attendance_date = $attendance_date;
                                $rider_attendance->clock_in_datetime = $attendance_datetime;
                                $rider_attendance->clock_in_latitude = '0';
                                $rider_attendance->clock_in_longitude = '0';
                                $rider_attendance->save();

                                $rider_attendance_action = new EmployeeAttendanceActionLog();
                                $rider_attendance_action->employee_id = $employee_id;
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
                                    $rider_attendance_action->employee_id = $employee_id;
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

                        $riders[] = $delivery_note->rider_id;
                    }
                }
            }
        }
    }
}
