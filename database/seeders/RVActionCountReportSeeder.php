<?php

use Illuminate\Database\Seeder;

class RVActionCountReportSeeder extends Seeder
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
            array('id' => 930, 'name' => 'Rv Action Count Report - View', 'module_id' => 9),
        ));


        DB::table('activity_trail_actions')->insert(array(
            array('id' => 733, 'screen_name' => 'Rv Action Count Report', 'action' => 'View'),
        ));


        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Rv Action Count Report', 'url'=>'admin.reports.rv_action_count_report.index', 'permission_id' => 930),
        ));
    }
}
