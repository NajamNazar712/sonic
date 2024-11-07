<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForEmployeeLeaveScreen extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 613, 'name' => 'Employee Leave - View', 'module_id' => 28),
            array('id' => 614, 'name' => 'Employee Leave - Update', 'module_id' => 28),
            array('id' => 615, 'name' => 'Employee Leave - Approve/Reject', 'module_id' => 28)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 465, 'screen_name' => 'Employee Leaves', 'action'=> 'View'),
            array('id' => 466, 'screen_name' => 'Employee Leaves', 'action'=> 'Excel Download')
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > Employee Leaves', 'url'=>'admin.human_resource.leave.index', 'permission_id' => 613)
        );
    }
}
