<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\Admin;
use App\Http\Models\HR\Employee;
use App\Http\Models\Rider;

class Seeder5202ForChangingAllHumanResourceIdToEmployeeId extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (DB::table('employee_leaves')->get() as $emp)
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
                    $emp->save();
                }
            }
        }

        foreach (DB::table('employee_attendances')->get() as $emp)
        {
            dd($emp);
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
                    $emp->employee_id = $employee->id;
                    $emp->save();
                }
            }
        }

        foreach (DB::table('employee_attendance_action_logs')->get() as $emp)
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
                    $emp->employee_id = $employee->id;
                    $emp->save();
                }
            }
        }
    }
}
