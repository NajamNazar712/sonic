<?php

use Illuminate\Database\Seeder;

class MmsSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('global_settings')->insert(array(
            array('id' => 117, 'setting_value' =>  0 , 'type' => "mms_setting", 'text' => '15636, 16292, 15587, 17363, 17747, 3324, 1091, 10104, 20040, 22343, 22395, 22230, 22946, 14110, 19507, 14781', 'created_at'=> \Carbon\Carbon::now()),
        ));
    }
}
