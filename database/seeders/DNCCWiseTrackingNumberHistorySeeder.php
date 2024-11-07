<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DNCCWiseTrackingNumberHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Permission and Activity Trail
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('module_permissions')->insert(array(
            array('id' => 1012, 'name' => 'DNCC wise Tracking number info - View', 'module_id' => 8),
        ));
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 811, 'screen_name' => 'DNCC wise Tracking number info', 'action' => 'View'),
        ));
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 812, 'screen_name' => 'DNCC wise Tracking number info', 'action' => 'Excel Download'),
        ));
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Finance > DNCC wise Tracking number info', 'url'=>'admin.finance.dncc_wise_tracking_number_info.index', 'permission_id' => 1012),
        ));
    }
}
