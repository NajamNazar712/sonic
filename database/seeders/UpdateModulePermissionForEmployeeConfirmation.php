<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForEmployeeConfirmation extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
       
        DB::table('module_permissions')->insert(array(
            array('id' => 783, 'name' => 'Employee Confirmation', 'module_id' => 28),
            array('id' => 786, 'name' => 'Employee Confirmation Report', 'module_id' => 9),
        ));

        

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 572, 'screen_name' => 'Employee Confirmation', 'action'=> 'View'),
            array('id' => 573, 'screen_name' => 'Employee Confirmation', 'action'=> 'Excel Download'),
            array('id' => 576, 'screen_name' => 'Employee Confirmation Report', 'action'=> 'View'),
            array('id' => 577, 'screen_name' => 'Employee Confirmation Report', 'action'=> 'Excel Download'),
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > Employee Confirmation', 'url'=>'admin.human_resource.employee_confirmation.index', 'permission_id' => 783),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Employee Confirmation Report', 'url'=>'admin.reports.employee_confirmation.index', 'permission_id' => 786),
        ));
    }
}
