<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\GlobalSettings;

class UpdateGlobalSettingForLatestEmployeeIDTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $global_setting = GlobalSettings::where('type', 'latest_employee_id');

        if(!$global_setting->exists()){
            GlobalSettings::create([
                'setting_value' =>  3000,
                'type' => 'latest_employee_id',
                'text' => 'For Creation of Employees'
            ]);
        }

    }
}
