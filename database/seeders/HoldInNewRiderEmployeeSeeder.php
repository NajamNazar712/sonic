<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Http\Models\Rider;
use App\Http\Models\HR\Employee;
use App\Http\Models\Admin\GlobalSettings;

class HoldInNewRiderEmployeeSeeder extends Seeder
{
    public function run()
    {
        Rider::where('operation_rider_id', 2)->update(['status'=> 0]);

        $faker = Faker::create();
        $globalSetting = GlobalSettings::firstOrCreate(
            ['type' => 'latest_employee_id'],
            ['setting_value' => 0]
        );

        $riders = [];
        $employees = [];
        $nameIndex = 0;

        $names = [
            'NSA/OSA',
            'Incomplete Address',
            'Hold for Self Collection',
            'Friday/ Saturday Closed',
            'Restricted Area',
            'Hold in OPS',
            'Damaged',
            'Delivery Stopped',
            'Wrong Destination'
        ];

        for ($i = 0; $i < 9; $i++) {
            $traxIdValue = $globalSetting->setting_value + 1;
            $globalSetting->update(['setting_value' => $traxIdValue]);

            $traxId = 'Trax' . str_pad($traxIdValue, 5, '0', STR_PAD_LEFT);

            $currentName = $names[$nameIndex];
            $nameIndex = ($nameIndex + 1) % count($names);

            $riderData = [
                'city_id' => 202,
                'name' => $currentName,
                'phone' => $faker->phoneNumber,
                'cnic' => $faker->unique()->numerify('###########'),
                'address' => $faker->address,
                'route_id' => null,
                'rider_main_category_id' => 2,
                'rider_category_id' => 1,
                'operation_rider_id' => 2,
                'status' => 1,
                'special_rider' => 0,
                'ccd' => 0,
                'pin' => bcrypt(1234),
                'dummy_pin' => 1234,
                'area_id' => null,
                'created_by' => 1,
                'trax_id' => $traxId,
                'rider_type_id' => 1,
                'shift_id' => 1,
                'incentive_amount' => null,
                'allow_delivered_status' => null,
            ];

            $employeeData = [
                'city_id' => $riderData['city_id'],
                'name' => $riderData['name'],
                'phone_number' => $riderData['phone'],
                'cnic' => $riderData['cnic'],
                'employee_type_id' => 2,
                'request_status_id' => 3,
                'status_id' => 3,
                'address' => $riderData['address'],
                'pin' => $riderData['dummy_pin'],
                'shift_id' => 1,
                'department_id' => 6,
                'trax_id' => $traxId,
                'rider_main_category' => $riderData['rider_main_category_id'],
                'rider_sub_category' => $riderData['rider_category_id'],
                'rider_type_id' => $riderData['rider_type_id'],
                'area_id' => $riderData['area_id'],
            ];

            $riders[] = $riderData;
            $employees[] = $employeeData;
        }

        Rider::insert($riders);
        Employee::insert($employees);

        foreach ($riders as $riderData) {
            $rider = Rider::where('trax_id', $riderData['trax_id'])->first();
            $employee = Employee::where('trax_id', $riderData['trax_id'])->first();
            if ($rider && $employee) {
                $rider->employee_id = $employee->id;
                $rider->save();
            }
        }

    }
}
