<?php

use Illuminate\Database\Seeder;

class OverallRVRCallHistoryReportSeeder extends Seeder
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
            array('id' => 950, 'name' => 'RVR Call History Report - View', 'module_id' => 9),
        ));


        DB::table('activity_trail_actions')->insert(array(
            array('id' => 761, 'screen_name' => 'RVR Call History Report', 'action' => 'View'),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 762, 'screen_name' => 'RVR Call History Report', 'action' => 'Excel Download'),
        ));


        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > RVR Call History Report', 'url'=>'admin.reports.rvr_call_history.index', 'permission_id' => 950),
        ));
    }
}
