<?php

use Illuminate\Database\Seeder;

class ActivityTrailForCsatReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 899, 'name' => 'CSAT Report - View', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 694, 'screen_name' => 'CSAT', 'action'=> 'View'),
            array('id' => 695, 'screen_name' => 'CSAT', 'action'=> 'Excel'),

        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > CSAT', 'url'=>'admin.reports.csat_report.index', 'permission_id' => 899),           
        ));

    }
}
