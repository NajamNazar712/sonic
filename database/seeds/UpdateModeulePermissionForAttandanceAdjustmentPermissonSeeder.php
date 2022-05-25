<?php

use Illuminate\Database\Seeder;

class UpdateModeulePermissionForAttandanceAdjustmentPermissonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 533, 'screen_name' => 'Employee Attendance Adjustment', 'action'=> 'View'),
            array('id' => 534, 'screen_name' => 'Employee Attendance Adjustment', 'action'=> 'Excel Download'),
        ));
        DB::table('module_permissions')->insert(array(
            array('id' => 717, 'name' => 'Employee Attendance Adjustment - View', 'module_id' => 28),
        ));
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Report > Return Revert Log', 'url'=>'admin.reports.revert.index', 'permission_id' => 653));

    }
}
