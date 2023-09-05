<?php

use Illuminate\Database\Seeder;

class ActivityTrailForFintechReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 896, 'name' => 'Fintech - Permission', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 691, 'screen_name' => 'Fintech', 'action'=> 'View'),
            array('id' => 692, 'screen_name' => 'Fintech', 'action'=> 'Excel'),

        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Fintech', 'url'=>'admin.reports.fintech_report.index', 'permission_id' => 896),           
        ));

    }
}
