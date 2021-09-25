<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForAppNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 601, 'name' => 'App Notifications - View', 'module_id' => 13),
            array('id' => 602, 'name' => 'App Notifications - Update', 'module_id' => 13),
            array('id' => 603, 'name' => 'App Notifications - Enable/Disable', 'module_id' => 13)
        ));
    }
}
