<?php

use Illuminate\Database\Seeder;

class ActivtyTrailForLogisticsReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('module_permissions')->insert(array(
            array('id' => 916, 'name' => 'Logistics Report - Permission', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 718, 'screen_name' => 'Logistics Report ', 'action'=> 'View'),
            array('id' => 719, 'screen_name' => 'Logistics Report ', 'action'=> 'Excel'),

        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Logistics Report', 'url'=>'admin.reports.logistic.index', 'permission_id' => 916),           
        ));

    }
}
