<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRiderPickedReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 880, 'name' => 'Rider-Picked Status Report (W/O Arrival) - View', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 673, 'screen_name' => 'Rider-Picked Status Report (W/O Arrival)', 'action'=> 'View'),
            array('id' => 674, 'screen_name' => 'Rider-Picked Status Report (W/O Arrival)', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
        array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Rider-Picked Status Report (W/O Arrival)', 'url'=>'admin.reports.rider_picked.index', 'permission_id' => 880));
    }
}
