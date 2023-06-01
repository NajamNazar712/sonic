<?php

use Illuminate\Database\Seeder;

class AdminRoleModulePermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_role_module_permissions')->truncate();

        $permission_id = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,33,34,35,36,37,38,39,105,106,107,108,40,41,42,43,44,45,46,47,48,49,50,51,32,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,81,82,83,84,85,86,87,104);

        for($i=0;$i<count($permission_id);$i++) {

            if($permission_id[$i]==1)
            {
                $roles = array(2,3,4,5,6,7,8,9,10,11,12,13,14,15,16);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==2)
            {
                $roles = array(6,4,3,2,15,13,12,16,5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==3)
            {
                $roles = array(6,4,3,2,15,13,12,16,8,9,10,5,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==4)
            {
                $roles = array(6,13,15);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==5)
            {
                $roles = array(6,4,2,15,12,16);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==6)
            {
                $roles = array(4,2,12,16);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==7)
            {
                $roles = array(4,2);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==8)
            {
                $roles = array(2);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==9)
            {
                $roles = array(2);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==10)
            {
                $roles = array(2);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==11)
            {
                $roles = array(6,13,4,2,15,12,16,3);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==12)
            {
                $roles = array(2);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==13)
            {
                $roles = array(4,2);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==14)
            {
                $roles = array(2);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==15)
            {
                $roles = array(4,2,12,16,15,6);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==16)
            {
                $roles = array(2);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==17)
            {
                $roles = array(6,4,3,13,15,12,16,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==18)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==19)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==20)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==21)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==22)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==23)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==24)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==25)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==26)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==27)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==28)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==29)
            {
                $roles = array(3,6,15,13);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==30)
            {
                $roles = array(3,6,15,13,8,9);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==31)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==33)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==34)
            {
                $roles = array(3,6,15,13);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==35)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==36)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==37)
            {
                $roles = array(3,6,15,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==38)
            {
                $roles = array(3,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==39)
            {
                $roles = array(3,6,15,13);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==105)
            {
                $roles = array(3,6,15,8,9,10,14,2);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==106)
            {
                $roles = array(3,6,15,8,9,10,14,2);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==107)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==108)
            {
                $roles = array(3,6,15,13);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==40)
            {
                $roles = array(3,6,15,8,9,10,14,2);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==41)
            {
                $roles = array(3,8,9,10,14,2);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==42)
            {
                $roles = array(3,6,15,8,9,10,14,2);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==43)
            {
                $roles = array(3,8,9,10,14,2);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==44)
            {
                $roles = array(3,6,15,13);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==45)
            {
                $roles = array(6,15,13);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==46)
            {
                $roles = array(6,15,13);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==47)
            {
                $roles = array(3,6,15,8,9,10,11,13);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==48)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==49)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==50)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==51)
            {
                $roles = array(3,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==32)
            {
                $roles = array(3,6,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==52)
            {
                $roles = array(2,14);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==53)
            {
                $roles = array(2,14);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==54)
            {
                $roles = array(2,14);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==55)
            {
                $roles = array(2,14);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==56)
            {
                $roles = array(2,14);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==57)
            {
                $roles = array(2,14);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==58)
            {
                $roles = array(2,14);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==59)
            {
                $roles = array(2,14,6);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==60)
            {
                $roles = array(2,14);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==61)
            {
                $roles = array(2,14,6,4,15);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==62)
            {
                $roles = array(2,14);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==63)
            {
                $roles = array(2,14);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==64)
            {
                $roles = array(6,4,3,15,13,12,16,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==66)
            {
                $roles = array(6,4,3,15,13,12,16,8,9,10,2,14,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==67)
            {
                $roles = array(6,4,3,15,13,12,16,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==65)
            {
                $roles = array(6,3,15,13,8,9,10,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==68)
            {
                $roles = array(6,3,15,13,8,9,10,2,14);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==69)
            {
                $roles = array(6,3,15);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==70)
            {
                $roles = array(6,4,3,15,13,12,16,8,9,10,2,14);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==71)
            {
                $roles = array(6,4,3,2,15,13,8,9);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==72)
            {
                $roles = array(4,12,16);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==73)
            {
                $roles = array(4,12,16);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==74)
            {
                $roles = array(4,12,16);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==75)
            {
                $roles = array(6,4,3,2,15,12,16,14,8);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==76)
            {
                $roles = array(6,3,2,15,13,8,9,10,5,11);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==77)
            {
                $roles = array(6,3,2,5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==78)
            {
                $roles = array(6,3,2,15,13,8,5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==79)
            {
                $roles = array(6,3,2,15,13,8,9,10,4,12,16,5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==80)
            {
                $roles = array(6,3,15,13,8,9,10);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==88)
            {
                $roles = array(6,3,15,2,4,8,9,5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==89)
            {
                $roles = array(3,5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==90)
            {
                $roles = array(3);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==91)
            {
                $roles = array(3);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==92)
            {
                $roles = array(3,6,15,8,9,5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==93)
            {
                $roles = array(3);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==94)
            {
                $roles = array(3);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==95)
            {
                $roles = array(3);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==96)
            {
                $roles = array(3,6,15,8,9,5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==97)
            {
                $roles = array(3);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==98)
            {
                $roles = array(3);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==99)
            {
                $roles = array(3);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==100)
            {
                $roles = array(6,15,5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==101)
            {
                $roles = array(6);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==102)
            {
                $roles = array(6);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==103)
            {
                $roles = array(6,15,5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==81)
            {
                $roles = array(6,5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==82)
            {
                $roles = array(5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==83)
            {
                $roles = array(5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==84)
            {
                $roles = array(5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==85)
            {
                $roles = array(5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==86)
            {
                $roles = array(5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==87)
            {
                $roles = array(5);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }
            elseif($permission_id[$i]==104)
            {
                $roles = array(3);

                for($j=0;$j<count($roles);$j++)
                {
                    DB::table('admin_role_module_permissions')->insert(array(
                        array('role_id' => $roles[$j], 'permission_id' => $permission_id[$i])
                    ));
                }

            }

            elseif($permission_id[$i]==862)
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
