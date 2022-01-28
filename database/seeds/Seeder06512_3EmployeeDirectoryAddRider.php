<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Rider;
use App\Http\Models\HR\Employee;

class Seeder06512_3EmployeeDirectoryAddRider extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $riders = Rider::whereIn('trax_id',['Trax06512','Trax06513'])->get();
        foreach ($riders as $rider)
        {
            $employee = new Employee();
            $employee->trax_id = $rider->trax_id;
            $employee->name = $rider->name;
            $employee->employee_gender_id = 1;
            $employee->city_id = $rider->city_id;
            $employee->cnic = $rider->cnic;
            $employee->phone_number = $rider->phone;
            $employee->employee_type_id = 2;
            $employee->request_status_id = 3;
            $employee->status_id = 3;
            $employee->address = $rider->address;
            $employee->rider_main_category = $rider->rider_main_category_id;
            $employee->rider_sub_category = $rider->rider_category_id;
            $employee->rider_type_id = $rider->rider_type_id;
            $employee->pin = $rider->dummy_pin;
            $employee->shift_id = $rider->shift_id;
            $employee->department_id = 6;
            $employee->save();

            $rider->employee_id = $employee->id;
            $rider->update();
        }
    }
}
