<?php

use Illuminate\Database\Seeder;

class OperationsPerformanceModulePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 892, 'name' => 'Operations Performance Report', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 684, 'screen_name' => 'Operations Performance Report', 'action'=> 'View'),
            array('id' => 685, 'screen_name' => 'Operations Performance Report', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Operations Performance Report', 'url'=>'admin.reports.operations_performance.index', 'permission_id' => 892),
        ));
    }
}
