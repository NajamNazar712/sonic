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
        $riders = Rider::whereIn('id',[4875, 3130, 3136, 3142, 3144, 3145, 3146, 3151, 3153, 3155, 3162, 3170, 3178, 3180, 3185, 3187, 3188, 3189, 3190, 3192, 3193, 3196, 3199, 3208, 3215, 3216, 3218, 3220, 3228, 3229, 3246, 3250, 3251, 3252, 3255, 3260, 3268, 3277, 3278, 3279, 3280, 3281, 3288, 3294, 3296, 3299, 3301, 3304, 3305, 3322, 3325, 3331, 3335, 3337, 3338, 3341, 3345, 3346, 3351, 3353, 3356, 3357, 3358, 3359, 3360, 3361, 3362, 3367, 3369, 3370, 3374, 3376, 3377, 3378, 3379, 3383, 4196, 4671, 4749, 4772, 4922, 4051, 4426, 4052, 4429, 4125, 4016, 4015, 4908, 4150, 4927, 4010, 4014, 4187, 5144, 4013, 3472, 5118, 4718, 4815, 4397, 5074, 4667, 5142])->get();
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
