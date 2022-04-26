<?php

use Illuminate\Database\Seeder;

class RCPSmsCronTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('global_settings')->insert(array(
            array('setting_value' => 0, 'type' => 'rcp_sms_cron_time', 'text' => '12:00')
        ));
    }
}
