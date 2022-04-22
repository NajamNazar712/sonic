<?php

use Illuminate\Database\Seeder;


class rcp_sms_cron_time extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

        public function run()
    {
        DB::table('global_settings')->insert(array(
            array('id' => 74, 'setting_value' => 0, 'type' => 'rcp_sms_cron_time', 'text' => '09')
        ));
    }
}
