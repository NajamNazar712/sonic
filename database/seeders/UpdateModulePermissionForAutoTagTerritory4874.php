<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForAutoTagTerritory4874 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        
        DB::table('module_permissions')->insert(array(
            array('id' => 697, 'name' => 'Auto Tag Territory', 'module_id' => 14),
            array('id' => 698, 'name' => 'Auto Tag Territory - Add/Edit', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 518, 'screen_name' => 'Auto Tag Territory', 'action'=> 'View'),
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Auto Tag Territory', 'url'=>'admin.settings.auto_tag_territories.index', 'permission_id' => 697),
        ));
    }
}
