<?php

use Illuminate\Database\Seeder;

class ModulePermissionForQuickScannedReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 869, 'name' => 'Quick Scanned Report - View', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 659, 'screen_name' => 'Quick Scanned Report', 'action'=> 'View'),
            array('id' => 660, 'screen_name' => 'Quick Scanned Report', 'action'=> 'Excel Download'),

        ));
       
        $permission_id = array(869);
        for($i=0;$i<count($permission_id);$i++) {
            if($permission_id[$i]==869)
            {
                $roles = array(51,90,83,74,53,49,37,32,29,28,6);
                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }
            }
        }

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'CRM > CRM Dashboard', 'url'=>'admin.crm.dashboard.index', 'permission_id' => 869),
        ));
    }
}
