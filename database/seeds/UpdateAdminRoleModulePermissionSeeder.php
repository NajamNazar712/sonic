<?php

use Illuminate\Database\Seeder;

class UpdateAdminRoleModulePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_role_module_permissions')->insert(array(
            array('role_id' => 3, 'permission_id' => 901),
            array('role_id' => 4, 'permission_id' => 901),
            array('role_id' => 6, 'permission_id' => 901),
            array('role_id' => 8, 'permission_id' => 901),
            array('role_id' => 9, 'permission_id' => 901),
            array('role_id' => 10, 'permission_id' => 901),
            array('role_id' => 11, 'permission_id' => 901),
            array('role_id' => 12, 'permission_id' => 901),
            array('role_id' => 13, 'permission_id' => 901),
            array('role_id' => 15, 'permission_id' => 901),
            array('role_id' => 18, 'permission_id' => 901),
            array('role_id' => 19, 'permission_id' => 901),
            array('role_id' => 21, 'permission_id' => 901),
            array('role_id' => 23, 'permission_id' => 901),
            array('role_id' => 26, 'permission_id' => 901),
            array('role_id' => 27, 'permission_id' => 901),
            array('role_id' => 28, 'permission_id' => 901),
            array('role_id' => 29, 'permission_id' => 901),
            array('role_id' => 32, 'permission_id' => 901),
            array('role_id' => 33, 'permission_id' => 901),
            array('role_id' => 37, 'permission_id' => 901),
            array('role_id' => 41, 'permission_id' => 901),
            array('role_id' => 43, 'permission_id' => 901),
            array('role_id' => 44, 'permission_id' => 901),
            array('role_id' => 46, 'permission_id' => 901),
            array('role_id' => 49, 'permission_id' => 901),
            array('role_id' => 50, 'permission_id' => 901),
            array('role_id' => 51, 'permission_id' => 901),
            array('role_id' => 53, 'permission_id' => 901),
            array('role_id' => 54, 'permission_id' => 901),
            array('role_id' => 55, 'permission_id' => 901),
            array('role_id' => 60, 'permission_id' => 901),
            array('role_id' => 64, 'permission_id' => 901),
            array('role_id' => 65, 'permission_id' => 901),
            array('role_id' => 67, 'permission_id' => 901),
            array('role_id' => 72, 'permission_id' => 901),
            array('role_id' => 74, 'permission_id' => 901),
            array('role_id' => 75, 'permission_id' => 901),
            array('role_id' => 76, 'permission_id' => 901),
            array('role_id' => 78, 'permission_id' => 901),
            array('role_id' => 83, 'permission_id' => 901),
            array('role_id' => 84, 'permission_id' => 901),
            array('role_id' => 85, 'permission_id' => 901),
            array('role_id' => 89, 'permission_id' => 901),
            array('role_id' => 90, 'permission_id' => 901),
            array('role_id' => 91, 'permission_id' => 901),
            array('role_id' => 92, 'permission_id' => 901),
            array('role_id' => 93, 'permission_id' => 901),
            array('role_id' => 95, 'permission_id' => 901),
            array('role_id' => 96, 'permission_id' => 901),
            array('role_id' => 99, 'permission_id' => 901),
            array('role_id' => 100, 'permission_id' => 901),
            array('role_id' => 101, 'permission_id' => 901),
            array('role_id' => 103, 'permission_id' => 901),
            array('role_id' => 105, 'permission_id' => 901),
            array('role_id' => 106, 'permission_id' => 901),
            array('role_id' => 107, 'permission_id' => 901),
            array('role_id' => 115, 'permission_id' => 901),
            array('role_id' => 116, 'permission_id' => 901),
            array('role_id' => 117, 'permission_id' => 901),
            array('role_id' => 120, 'permission_id' => 901),
            array('role_id' => 121, 'permission_id' => 901),
            array('role_id' => 122, 'permission_id' => 901),
            array('role_id' => 123, 'permission_id' => 901),
            array('role_id' => 124, 'permission_id' => 901),
            array('role_id' => 125, 'permission_id' => 901),
            array('role_id' => 127, 'permission_id' => 901),
            array('role_id' => 128, 'permission_id' => 901),
            array('role_id' => 131, 'permission_id' => 901),

            //give permission for track button and view 
            array('role_id' => 24, 'permission_id' => 901), //view
            array('role_id' => 24, 'permission_id' => 908), //track
        ));
    }
}
