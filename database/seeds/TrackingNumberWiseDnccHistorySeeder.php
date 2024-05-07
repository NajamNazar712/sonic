<?php

use Illuminate\Database\Seeder;

class TrackingNumberWiseDnccHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Permission and Activity Trail
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('module_permissions')->insert(array(
            array('id' => 984, 'name' => 'Tracking number wise DNCC info - View', 'module_id' => 8),
        ));
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 789, 'screen_name' => 'Tracking number wise DNCC info', 'action' => 'View'),
        ));
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 790, 'screen_name' => 'Tracking number wise DNCC info', 'action' => 'Excel Download'),
        ));
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Finance > Tracking number wise DNCC info', 'url'=>'admin.finance.tracking_number_wise_dncc_history.index', 'permission_id' => 984),
        ));
    }
}
