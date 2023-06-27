<?php

use Illuminate\Database\Seeder;

class ModulePermissionForQuickScannedReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 877, 'name' => 'Quick Scanned Report - View', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 667, 'screen_name' => 'Quick Scanned Report', 'action'=> 'View'),
            array('id' => 668, 'screen_name' => 'Quick Scanned Report', 'action'=> 'Excel Download'),

        ));
       
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Quick Scanned Report', 'url'=>'admin.reports.quick_scanned_report.index', 'permission_id' => 877),
        ));
    }
}
