<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailActionForLastMileSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /*DB::table('module_permissions')->insert(array(
            array('id' => 565, 'name' => 'Last Mile Status Cron Time - View', 'module_id' => 14),
        ));*/

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 431, 'screen_name' => 'Last Mile Status Cron Time', 'action'=> 'View'),
        ));
    }
}
