<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForEmployeeLeaveScreenHodApproveReject extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 706, 'name' => 'Employee Leave - HOD - Approve/Reject', 'module_id' => 28),
        ));
    }
}
