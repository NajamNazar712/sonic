<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForSpecialApprovalReport4491 extends Seeder
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
            array('id' => 688, 'name' => 'CRM Special Approval', 'module_id' => 9)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 513, 'screen_name' => 'CRM Special Approval', 'action'=> 'View'),
            array('id' => 514, 'screen_name' => 'CRM Special Approval', 'action'=> 'Excel Download'),
        ));


        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > CRM Special Approval', 'url'=>'admin.reports.crm_special_approval.index', 'permission_id' => 688));
       
        DB::table('admin_role_module_permissions')->insert(array(
            array('permission_id' => 688, 'role_id' => 37),
            array('permission_id' => 688, 'role_id' => 83),
        ));
    }
}
