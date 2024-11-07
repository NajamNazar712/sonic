<?php

use Illuminate\Database\Seeder;

class OPSModulePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 911, 'name' => 'OPS Report - View', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 708, 'screen_name' => 'OPS Report', 'action'=> 'View'),
            array('id' => 709, 'screen_name' => 'OPS Report', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > OPS Report', 'url'=>'admin.reports.ops_report.index', 'permission_id' => 911),
        ));
    }
}
