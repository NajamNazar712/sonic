<?php

use Illuminate\Database\Seeder;
use App\Http\Models\HR\Employee;
use App\Http\Models\Rider;
use App\Http\Models\Admin\Admin;

class Seeder4236ForEmployeeDirectory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employees = array(
            array('trax_id' => '','name' => '','gender' => '','city' => '','cnic' => '','phone' => '','type' => 'designation' => '','department' => '','shift' => '','status' => ''),
        );

        foreach ($employees as $employee)
        {
            $emp = new Employee();
            $emp->trax_id = $employee['trax_id'];
            $emp->name = $employee['name'];
            $emp->employee_gender_id = $employee['gender'];
            $emp->city_id = $employee['city'];
            $emp->cnic = $employee['cnic'];
            $emp->phone_number = $employee['phone'];
            $emp->employee_type_id = $employee['type'];
            $emp->request_status_id = 3;
            if($employee['status'] == 1) {
                $emp->status_id = 3;
            }
            else{
                $emp->status_id = 2;
            }
            $emp->designation_id = $employee['designation'];
            $emp->department_id = $employee['department'];
            $emp->shift_id = $employee['shift'];
            $emp->save();


            if($employee['type'] == 1)
            {
                $admin = Admin::where('trax_id',$employee['trax_id'])
                    ->where('trax_id','!=',null);

                if($admin->exists()) {
                    $admin = $admin->first();

                    $admin->employee_id = $emp->id;
                    $admin->designation_id = $employee['designation'];
                    $admin->update();
                }
            }
            else{
                $rider = Rider::where('trax_id',$employee['trax_id'])
                    ->where('trax_id','!=',null);

                if($rider->exists()) {
                    $rider = $rider->first();

                    $rider->employee_id = $emp->id;
                    $rider->update();
                }
            }
        }
    }
}
