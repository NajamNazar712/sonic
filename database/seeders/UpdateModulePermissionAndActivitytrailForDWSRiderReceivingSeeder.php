<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionAndActivitytrailForDWSRiderReceivingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 670, 'name' => 'Rider Receiving DWS', 'module_id' => 3),
        ));
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 491, 'screen_name' => 'Rider Receiving DWS', 'action'=> 'View'),
            array('id' => 492, 'screen_name' => 'Rider Receiving DWS', 'action'=> 'Excel Download'),
        ));

    }
}
