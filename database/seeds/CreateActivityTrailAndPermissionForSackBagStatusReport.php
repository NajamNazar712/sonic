<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateActivityTrailAndPermissionForSackBagStatusReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 936, 'name' => 'Canvas Bag Status Report - View', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 743, 'screen_name' => 'Canvas Bag Status', 'action' => 'View'),
            array('id' => 744, 'screen_name' => 'Canvas Bag Status', 'action' => 'Excel'),

        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Canvas Bag Status', 'url' => 'admin.reports.sack_bag_status.index', 'permission_id' => 936),
        ));
    }
}
