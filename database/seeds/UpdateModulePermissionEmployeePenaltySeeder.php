<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionEmployeePenaltySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 829, 'name' => 'Employee Penalty - View', 'module_id' => 28),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 627, 'screen_name' => 'Employee Penalty', 'action'=> 'View'),
            array('id' => 628, 'screen_name' => 'Employee Penalty', 'action'=> 'Excel Download'),
        ));
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > Employee Penalty', 'url'=>'admin.human_resource.employee_penalty.index', 'permission_id' => 829));
    }
}
