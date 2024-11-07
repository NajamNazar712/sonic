<?php

use Illuminate\Database\Seeder;

class LogisticReportSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('global_settings')->insert(array(
            array('setting_value' =>  0 , 'type' => "logistic_setting", 'created_at'=> \Carbon\Carbon::now()),
        ));
    }
}
