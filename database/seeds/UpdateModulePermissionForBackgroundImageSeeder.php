<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForBackgroundImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 891, 'name' => 'Background Image - View', 'module_id' => 14),
        ));
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 683, 'screen_name' => 'Background Image', 'action'=> 'View'),
        ));
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings  >  Support  >  Background Image', 'url'=>'admin.settings.background_image.index', 'permission_id' => 891)
        );
    }
}
