<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForProjectArrivalReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('module_permissions')->insert(array(
            array('id' => 879, 'name' => 'Project Arrival Report - View', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 671, 'screen_name' => 'Project Arrival Report Screen', 'action'=> 'View'),
            array('id' => 672, 'screen_name' => 'Project Arrival Report Screen', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
        array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Project Arrival Report', 'url'=>'admin.reports.project_arrival.index', 'permission_id' => 879));

    }
}
