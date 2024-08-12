<?php

use App\Http\Models\Admin\Admin;
use App\Http\Models\HR\Employee;
use App\Http\Models\HR\EmployeeDesignation;
use Illuminate\Database\Seeder;

class MakeBotAgentUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employee_id = null;
        $admin = new Admin();

        $admin->name = $request->input('name');
        $admin->email = $request->input('email');
        $admin->phone_number = $request->input('phone_number');
        $admin->official_phone_number = $request->input('official_phone_number');
        $admin->role_id = $request->input('role_id');
        $admin->default_hub_id = $request->input('default_hub');
        $admin->password = bcrypt($request->input('pin'));
        $admin->dummy_pin = $request->input('pin');
        $admin->shift_id = $request->input('shift_id');
        $admin->designation_id = $request->input('designation_id');

            $admin->cnic = $request->input('cnic');
            if ($request->trax_id != null) {
                $trax_id = $request->trax_id;
                $employee = Employee::where('trax_id', $request->trax_id);
                if ($employee->exists()) {
                    $employee = $employee->first();
                    $employee_id = $employee->id;
                } else {
                    $employee = new Employee();
                    $employee->trax_id = $trax_id;
                    $employee->name = $request->name;
                    $employee->city_id = $request->default_hub;
                    $employee->cnic = $request->cnic;
                    $employee->phone_number = $request->phone_number;
                    $employee->official_phone_number = $request->official_phone_number;
                    $employee->employee_type_id = 1;
                    $employee->request_status_id = 3;
                    $employee->status_id = 3;
                    $employee->official_email = $request->email;
                    $employee->designation_id = $request->designation_id;
                    $employee->department_id = EmployeeDesignation::find($request->designation_id)->department_id ?? null;
                    $employee->pin = $request->pin;
                    $employee->shift_id = $request->shift_id;
                    $employee->save();

                    $employee_id = $employee->id;

                }

            } else {
                $global_setting = GlobalSettings::where('type', 'latest_employee_id');

                if ($global_setting->exists()) {
                    $global_setting = $global_setting->first();
                    $trax_id = $global_setting->setting_value + 1;
                    $global_setting->setting_value = $trax_id;
                    $global_setting->save();
                    $trax_id = 'Trax' . str_pad($trax_id, 5, '0', STR_PAD_LEFT);

                    $employee = new Employee();
                    $employee->trax_id = $trax_id;
                    $employee->name = $request->name;
                    $employee->city_id = $request->default_hub;
                    $employee->cnic = $request->cnic;
                    $employee->phone_number = $request->phone_number;
                    $employee->official_phone_number = $request->official_phone_number;
                    $employee->employee_type_id = 1;
                    $employee->request_status_id = 3;
                    $employee->status_id = 3;
                    $employee->official_email = $request->email;
                    $employee->designation_id = $request->designation_id;
                    $employee->department_id = EmployeeDesignation::find($request->designation_id)->department_id ?? null;
                    $employee->pin = $request->pin;
                    $employee->shift_id = $request->shift_id;
                    $employee->save();

                    $employee_id = $employee->id;
                } else {
                    $trax_id = null;
                }
            }

            $admin->trax_id = $trax_id;
            $admin->employee_id = $employee_id;

        $admin->save();
    }
}
