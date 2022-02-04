<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForCRMCountReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 673, 'name' => 'CRM Count Report', 'module_id' => 9)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 499, 'screen_name' => 'CRM Count Report', 'action'=> 'View'),
            array('id' => 500, 'screen_name' => 'CRM Count Report', 'action'=> 'Excel Download'),
        ));


        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > CRM Counts', 'url'=>'admin.reports.crm_count.index', 'permission_id' => 673),
        ));
    }
}
