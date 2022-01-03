<?php

use Illuminate\Database\Seeder;
use \App\Http\Models\HR\Employee;
use \App\Http\Models\Admin\Admin;

class SeederToAddThreeEmployee extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employees = array(
            array('trax_id'=>'Trax04837','name'=>'Arslan Ramzan','city'=>'223','cnic'=>'35202-0191637-1','phone'=>'0343-2518363','department'=>2,'designation'=>19,'email'=>''),
            array('trax_id'=>'Trax05064','name'=>'Muhammad Daniyal Khan','city'=>'202','cnic'=>'42201-5613736-5','phone'=>'0333-3848578','department'=>11,'designation'=>160,'email'=>''),
            array('trax_id'=>'Trax00952','name'=>'Umair Riaz','city'=>'293','cnic'=>'36502-3029818-9','phone'=>'0301-4944796','department'=>4,'designation'=>58,'email'=>'umair.riaz@trax.pk
'),
        );

        foreach ($employees as $employee)
        {
            try {
                $emp = new Employee();
                $emp->trax_id = $employee['trax_id'];
                $emp->name = $employee['name'];
                $emp->employee_gender_id = 1;
                $emp->city_id = $employee['city'];
                $emp->cnic = $employee['cnic'];
                $emp->phone_number = $employee['phone'];
                $emp->employee_type_id = 1;
                $emp->request_status_id = 3;
                $emp->status_id = 3;
                $emp->designation_id = $employee['designation'];
                $emp->department_id = $employee['department'];
                $emp->official_email = $employee['email'];
                $emp->shift_id = 1;
                $emp->first_inactive = 1;
                $emp->save();



                $admin = Admin::where('trax_id', $employee['trax_id'])
                    ->where('trax_id', '!=', null);

                if ($admin->exists()) {
                    $admin = $admin->first();

                    $admin->employee_id = $emp->id;
                    $admin->designation_id = $employee['designation'];
                    $admin->update();
                }
            }
            catch (Exception $e)
            {
                echo $e->getMessage();
            }
        }
    }
}
