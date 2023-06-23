<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiderRemarksAssignPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $permission_id = array(862,863,864,865,866);
         
        for($i=0;$i<count($permission_id);$i++) {

            if($permission_id[$i]==862)
            {
                $roles = array(3,8,9,10,11,15,18,19,23,33,46,55,63,65,70,72,76,78,84,85,89,91,93,95,96,101,104,105,106,116,121,122,123);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }

            elseif($permission_id[$i]==863)
            {
                $roles = array(9, 8, 105, 10, 3, 93);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }


            elseif($permission_id[$i]==864)
            {
                $roles = array(9, 8, 105, 10, 3, 93);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }

            elseif($permission_id[$i]==865)
            {
                $roles = array(9, 8, 105, 10, 3, 93);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }

            elseif($permission_id[$i]==866)
            {
                $roles = array(9, 8, 105, 10, 3, 93);

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
