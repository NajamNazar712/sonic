<?php

use Illuminate\Database\Seeder;

class ModulePermissionForLeadCallStatusLog extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 870, 'name' => 'Leads Call Status (Make A Call) - Action', 'module_id' => 25),
            array('id' => 871, 'name' => 'Leads Call Status (End A Call) - Action', 'module_id' => 25),
        ));

        $permission_id = array(870, 871);
            
        for($i=0;$i<count($permission_id);$i++) {

            if($permission_id[$i]==870)
            {
                $roles = array(4,6,12,13,15,16,22,27,29,32,35,37,41,44,45,50,51,53,56,58,60,64,73,74,83,90,92,93,99,100,103,107,117,120);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==871)
            {
                
                $roles = array(4,6,12,13,15,16,22,27,29,32,35,37,41,44,45,50,51,53,56,58,60,64,73,74,83,90,92,93,99,100,103,107,117,120);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }

        }

    }
}
