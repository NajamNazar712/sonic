<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForSSRSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 793, 'name' => 'Summaries Sale Report - View', 'module_id' => 9)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 581, 'screen_name' => 'Summaries Sale Report', 'action'=> 'View'),
            array('id' => 582, 'screen_name' => 'Summaries Sale Report', 'action'=> 'Excel'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Summaries Sale (SSR)', 'url'=>'admin.reports.ssr.index', 'permission_id' => 793));

        DB::table('admin_role_module_permissions')->insert(array(
            array('role_id' => 44, 'permission_id' => 793),
            array('role_id' => 4, 'permission_id' => 793),
        ));
    }
}
