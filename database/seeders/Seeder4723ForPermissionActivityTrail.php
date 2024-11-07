<?php

use Illuminate\Database\Seeder;

class Seeder4723ForPermissionActivityTrail extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 682, 'name' => 'Cargo Manifest Draft Setting', 'module_id' => 14),
        ));

        DB::table('admin_role_module_permissions')->insert(array(
            array('permission_id' => 682, 'role_id' => 46),
            array('permission_id' => 682, 'role_id' => 23),
            array('permission_id' => 682, 'role_id' => 9),
            array('permission_id' => 682, 'role_id' => 8),
            array('permission_id' => 682, 'role_id' => 15),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 509, 'screen_name' => 'Cargo Manifest Draft Setting', 'action'=> 'View'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Supply Chain > Cargo Manifest Draft Setting', 'url'=>'admin.cargo_manifest.draft.setting', 'permission_id' => 682));

    }
}
