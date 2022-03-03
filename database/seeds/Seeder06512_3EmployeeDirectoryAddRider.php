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
        $riders = Rider::whereIn('id',[3386, 3411, 3533, 3592, 3918, 3935, 3954, 4019, 4059, 4096, 4176, 4238, 4270, 4389, 4390, 4424, 4468, 4539, 4586, 4634, 4806, 4822, 4853, 4918, 4951, 4974, 4978, 4979, 4980, 4981, 5002, 5017, 5063, 5119, 5133])->get();
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
