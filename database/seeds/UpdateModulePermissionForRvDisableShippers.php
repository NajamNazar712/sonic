<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRvDisableShippers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('global_settings')->insert(array(
            array('type' => 'rv_disable_shippers', 'setting_value' => 1, 'text' => '', 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 681, 'screen_name' => 'Rv Disable Shippers', 'action'=> 'View'),
            array('id' => 682, 'screen_name' => 'Rv Disable Shippers', 'action'=> 'Update')
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shippers > Rv Disable Shippers', 'url'=>'admin.settings.rv_disable_shippers.index', 'permission_id' => 889)
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 889, 'name' => 'Rv Disable Shippers - View', 'module_id' => 14),
        ));
    }
}
    