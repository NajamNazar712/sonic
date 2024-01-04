<?php

use Illuminate\Database\Seeder;

class IbftReportSeeder extends Seeder
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
            array('id' => 915, 'name' => 'Ibft Report - View', 'module_id' => 9),
        ));


        DB::table('activity_trail_actions')->insert(array(
            array('id' => 715, 'screen_name' => 'Ibft Report', 'action' => 'View'),
            array('id' => 716, 'screen_name' => 'Ibft Report ', 'action' => 'Excel Download'),
        ));


        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Ibft Report', 'url' => 'admin.reports.ibft_report.index', 'permission_id' => 915),
        ));
    }
}
