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
        DB::table('module_permissions')->insert(array(
            array('id' => 783, 'name' => 'Employee Confirmation', 'module_id' => 28),
            array('id' => 786, 'name' => 'Employee Confirmation Report', 'module_id' => 9),
        ));

        

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 572, 'screen_name' => 'Employee Confirmation', 'action'=> 'View'),
            array('id' => 573, 'screen_name' => 'Employee Confirmation', 'action'=> 'Excel Download'),
        ));
    }
}
