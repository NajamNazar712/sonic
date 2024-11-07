<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForPayslipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 596, 'name' => 'Employee Payslip', 'module_id' => 28),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 436, 'screen_name' => 'Payslip', 'action'=> 'View'),
            array('id' => 437, 'screen_name' => 'Payslip', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > Payslips', 'url'=>'admin.human_resource.payslip.index', 'permission_id' => 596)
        );
    }
}
