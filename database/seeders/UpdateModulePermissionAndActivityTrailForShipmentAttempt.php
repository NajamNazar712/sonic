<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateModulePermissionAndActivityTrailForShipmentAttempt extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 1061, 'name' => '2nd Attempt Performance Report', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 838, 'screen_name' => '2nd Attempt Performance Report View', 'action'=> 'View'),
            array('id' => 839, 'screen_name' => '2nd Attempt Performance Report Excel', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports >  2nd Attempt Performance Report', 'url' => 'admin.reports.shipment_attempt_performance.index', 'permission_id' => 1061),
        ));
    }
}
