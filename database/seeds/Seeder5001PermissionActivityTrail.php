<?php

use Illuminate\Database\Seeder;

class Seeder5001PermissionActivityTrail extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 705, 'name' => 'Rider Unresponsive Report', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 523, 'screen_name' => 'Rider Unresponsive Report', 'action'=> 'View'),
            array('id' => 524, 'screen_name' => 'Rider Unresponsive Report', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Rider Unresponsive Report', 'url'=>'admin.reports.rider_unresponsive_report.index', 'permission_id' => 705)
        );
    }
}
