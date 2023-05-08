<?php

use Illuminate\Database\Seeder;

class DeBriefingRoleSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('global_settings')->insert(array(
            array('setting_value' => 0 , 'type'=>'debriefing_role_setting', 'text'=> '18,49,26,21,32,100,103'),
        ));
    }
}
