<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\Admin;
use App\Http\Models\HR\Employee;
use App\Http\Models\Rider;
use App\Http\Models\HR\EmployeeLeave;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\Attendance\EmployeeAttendanceActionLog;

class Seeder5202ForChangingAllHumanResourceIdToEmployeeId extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*foreach (EmployeeLeave::all() as $emp)
        {
            if($emp->employee_type_id == 1)
            {
                $user = Admin::find($emp->employee_id);
            }
            else if($emp->employee_type_id == 2){
                $user = Rider::find($emp->employee_id);
            }

            if($user)
            {
                $trax_id = $user->trax_id;
                $employee = Employee::where('trax_id',$trax_id)->whereNotNull('trax_id');
                if($employee->exists())
                {
                    $employee = $employee->first();
                    $emp->employee_id = $employee->id;
                    $emp->update();
                }
            }
        }*/

        $employee_attendance = EmployeeAttendance::whereBetween('attendance_date', ['2022-05-25', '2022-07-20'])->groupBy('employee_id')->get();

        dd($employee_attendance);
        foreach ($employee_attendance as $emp)
        {
            if($emp->employee_type == 1)
            {
                $user = Admin::find($emp->employee_id);
            }
            else if($emp->employee_type == 2){
                $user = Rider::find($emp->employee_id);
            }

            if($user)
            {
                $trax_id = $user->trax_id;
                $employee = Employee::where('trax_id',$trax_id)->whereNotNull('trax_id');

                if($employee->exists())
                {
                    $employee = $employee->first();
                    EmployeeAttendance::whereBetween('attendance_date', ['2022-05-25', '2022-07-20'])->where('employee_id', $emp->employee_id)->update(['employee_id' => $employee->id]);
                }
            }
        }
        /*$action_logs = EmployeeAttendanceActionLog::whereBetween('attendance_date', ['2022-05-25', '2022-07-20'])->groupBy('employee_id')->get();
        foreach ($action_logs as $emp)
        {
            if($emp->employee_type == 1)
            {
                $user = Admin::find($emp->employee_id);
            }
            else if($emp->employee_type == 2){
                $user = Rider::find($emp->employee_id);
            }

            if($user)
            {
                $trax_id = $user->trax_id;
                $employee = Employee::where('trax_id',$trax_id)->whereNotNull('trax_id');
                if($employee->exists())
                {
                    $employee = $employee->first();
                    EmployeeAttendanceActionLog::whereBetween('attendance_date', ['2022-05-25', '2022-07-20'])->where('employee_id', $emp->employee_id)->update(['employee_id' => $employee->id]);
                }
            }
        }*/
    }
}