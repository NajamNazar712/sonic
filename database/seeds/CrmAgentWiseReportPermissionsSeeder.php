<?php

use Illuminate\Database\Seeder;

class CrmAgentWiseReportPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 784, 'name' => 'Crm Agent Wise Report - View', 'module_id' => 9),
            array('id' => 785, 'name' => 'Crm Agent Wise Report - Excel', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 574, 'screen_name' => 'Crm Agent Wise Report', 'action'=> 'View'),
            array('id' => 575, 'screen_name' => 'Crm Agent Wise Report', 'action'=> 'Download Excel'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > CRM Agent Wise Report', 'url'=>'admin.reports.crm_agent_wise_report.index', 'permission_id' => 784),
        ));

        DB::table('admin_role_module_permissions')->insert(array(
            array('role_id' => 37, 'permission_id' => 784),
            array('role_id' => 37, 'permission_id' => 785),

            array('role_id' => 6, 'permission_id' => 784),
            array('role_id' => 6, 'permission_id' => 785),

            array('role_id' => 83, 'permission_id' => 784),
            array('role_id' => 83, 'permission_id' => 785),
        ));

    }
}
