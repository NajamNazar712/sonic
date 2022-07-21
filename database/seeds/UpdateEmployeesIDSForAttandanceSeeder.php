<?php

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\Attendance\EmployeeAttendance;
use App\Http\Models\Admin\Attendance\EmployeeAttendanceActionLog;
use App\Http\Models\Rider;
use Illuminate\Database\Seeder;

class UpdateEmployeesIDSForAttandanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employees = \App\Http\Models\HR\Employee::whereNotNull('trax_id')->get();

        foreach ($employees as $employee) {

            if($employee->employee_type_id == 1)
            {
                $user = Admin::where('trax_id' ,$employee->trax_id)->where('status', 1);
            }
            else if($employee->employee_type_id == 2){
                $user = Rider::where('trax_id' ,$employee->trax_id)->where('status', 1);
            }
            if($user->exists()){
                $user = $user->first();
                EmployeeAttendance::where('employee_id', $user->id)->where('employee_type', $employee->employee_type_id)->where('id', '<', 993198)->update(['employee_id' => $employee->id]);
//                EmployeeAttendanceActionLog::where('employee_id', $user_id)->where('employee_type', $employee_type)->update(['employee_id' => $employee->id]);
            }
        }
    }
}
