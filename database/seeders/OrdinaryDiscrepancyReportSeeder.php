<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OrdinaryDiscrepancyReportSeeder extends Seeder
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
            array('id' => 901, 'name' => 'Operation Disorder Report - View', 'module_id' => 9),
        ));


        DB::table('activity_trail_actions')->insert(array(
            array('id' => 698, 'screen_name' => 'Operation Disorder Report', 'action' => 'View'),
            array('id' => 699, 'screen_name' => 'Operation Disorder Report ', 'action' => 'Excel Download'),
            array('id' => 700, 'screen_name' => 'Operation Disorder Report ', 'action' => 'Tracking'),
        ));


        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Reports > Operation Disorder Report', 'url' => 'admin.reports.operation_disorder_report.index', 'permission_id' => 901),
        ));
    }
}
