<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ActivityTrailForSMSCountReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        DB::table('module_permissions')->insert(array(
//            array('id' => 945, 'name' => 'SMS Count Report - Permission', 'module_id' => 9),
//        ));
//
//        DB::table('activity_trail_actions')->insert(array(
//            array('id' => 753, 'screen_name' => 'SMS Count Report ', 'action'=> 'View'),
//            array('id' => 754, 'screen_name' => 'SMS Count Report ', 'action'=> 'Excel'),
//
//        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > SMS Count Report', 'url'=>'admin.reports.sms.index', 'permission_id' => 945),           
        ));
    }
}
