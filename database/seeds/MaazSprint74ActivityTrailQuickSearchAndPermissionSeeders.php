<?php

use Illuminate\Database\Seeder;

class MaazSprint74ActivityTrailQuickSearchAndPermissionSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 396, 'screen_name' => 'Mapping (Cargo Manifest)', 'action'=> 'View'),
            array('id' => 397, 'screen_name' => 'Mapping (Cargo Manifest)', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings  >  Supply Chain  >  Mapping (Cargo Manifest)', 'url'=>'admin.cargo.mapping.manifest.index', 'permission_id' => 544),
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 544, 'name' => 'Mapping (Cargo Manifest)', 'module_id' => 32),
            array('id' => 548, 'name' => 'Mapping (Cargo Manifest) - Add', 'module_id' => 32),
            array('id' => 549, 'name' => 'Mapping (Cargo Manifest) - Edit', 'module_id' => 32),
            array('id' => 550, 'name' => 'Mapping (Cargo Manifest) - Enable/Disable', 'module_id' => 32),
            array('id' => 551, 'name' => 'Create Cargo Manifest', 'module_id' => 32),
        ));
    }
}
