<?php

namespace Database\Seeders;

use App\Http\Models\Admin\Admin;
use App\Http\Models\Admin\GlobalSettings;
use App\Http\Models\HR\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MakeCxDigitalUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employee_id = null;

        $name = 'CX Digitization account';
        $email = 'cxdigitization@trax.pk';
        $defaultHub = 202;
        $cnic = '44444-0310000-1';
        $phoneNo = 03200000000;
        $officialPhoneNo = 03200000000;
        $designationId = 51;
        $departmentId = 3;
        $shiftId = '';
        $pin = bcrypt(315513);
        $apiToken = 'TTYxcmJ2cWhWYnYwNEpOWWdETWNQZ3hPb21adHdxb1dwWlBwdFdURm5CeWF2Vlo1bFZiYjBNaXRKQm42682dcb489e724'; //uniqid(base64_encode(Str::random(60)));

        $admin = new Admin();

        $admin->name = $name;
        $admin->email = $email;
        $admin->phone_number = $phoneNo;
        $admin->official_phone_number = $officialPhoneNo;
        $admin->role_id = 37;
        $admin->default_hub_id = $defaultHub;
        $admin->password = $pin;
        $admin->dummy_pin = 123456;
        // $admin->shift_id = $shiftId; //Contractual Shift A
        $admin->designation_id = $designationId; //Return Confirmation Officer
        $admin->api_token = $apiToken;

        $admin->cnic = $cnic;

        $global_setting = GlobalSettings::where('type', 'latest_employee_id');

        if ($global_setting->exists()) {
            $global_setting = $global_setting->first();
            $trax_id = $global_setting->setting_value + 1;
            $global_setting->setting_value = $trax_id;
            $global_setting->save();
            $trax_id = 'Trax' . str_pad($trax_id, 5, '0', STR_PAD_LEFT);

            $employee = new Employee();
            $employee->trax_id = $trax_id;
            $employee->name = $name;
            $employee->city_id = $defaultHub;
            $employee->cnic = $cnic;
            $employee->phone_number = $phoneNo;
            $employee->official_phone_number = $officialPhoneNo;
            $employee->employee_type_id = 1;
            $employee->request_status_id = 3;
            $employee->status_id = 3;
            $employee->official_email = $email;
            $employee->designation_id = $designationId;
            $employee->department_id = $departmentId;
            $employee->staff_category_id = 3;
            $employee->pin = $pin;
            $employee->shift_id = $shiftId;
            $employee->save();

            $employee_id = $employee->id;
        } else {
            $trax_id = null;
        }

        $admin->trax_id = $trax_id;
        $admin->employee_id = $employee_id;

        $admin->save();
    }
}
