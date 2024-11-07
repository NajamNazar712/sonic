<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRiderPickupReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 823 , 'name' => 'Rider Wise Pickup Report', 'module_id' => 9)
        ));


        DB::table('activity_trail_actions')->insert(array(
            array('id' => 617, 'screen_name' => 'Rider Wise Pickup Report', 'action'=> 'View'),
            array('id' => 618, 'screen_name' => 'Rider Wise Pickup Report', 'action'=> 'Excel Download'),
        ));
    }
}
