<?php

use Illuminate\Database\Seeder;

class AdminRoleAssignPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_role_module_permissions')->insert(array(
            array('role_id' => 19, 'permission_id' => 758),
            array('role_id' => 19, 'permission_id' => 760),
            array('role_id' => 19, 'permission_id' => 759),
            array('role_id' => 19, 'permission_id' => 757),
            array('role_id' => 19, 'permission_id' => 747),
            array('role_id' => 19, 'permission_id' => 748),
            array('role_id' => 19, 'permission_id' => 749),
            array('role_id' => 3, 'permission_id' => 758),
            array('role_id' => 3, 'permission_id' => 760),
            array('role_id' => 3, 'permission_id' => 759),
            array('role_id' => 3, 'permission_id' => 757),
            array('role_id' => 3, 'permission_id' => 747),
            array('role_id' => 3, 'permission_id' => 748),
            array('role_id' => 3, 'permission_id' => 749),
            array('role_id' => 89, 'permission_id' => 759),
            array('role_id' => 89, 'permission_id' => 757),
            array('role_id' => 10, 'permission_id' => 757),
            array('role_id' => 10, 'permission_id' => 759),
            array('role_id' => 9, 'permission_id' => 757),
            array('role_id' => 9, 'permission_id' => 759),
            array('role_id' => 95, 'permission_id' => 757),
            array('role_id' => 95, 'permission_id' => 759),
            array('role_id' => 69, 'permission_id' => 750),
            array('role_id' => 70, 'permission_id' => 750),
            array('role_id' => 7, 'permission_id' => 750),
            array('role_id' => 2, 'permission_id' => 750),
            array('role_id' => 51, 'permission_id' => 751),
            array('role_id' => 51, 'permission_id' => 752),
            array('role_id' => 51, 'permission_id' => 753),
            array('role_id' => 51, 'permission_id' => 754),
            array('role_id' => 51, 'permission_id' => 755),
            array('role_id' => 51, 'permission_id' => 756),
            array('role_id' => 51, 'permission_id' => 745),
            array('role_id' => 51, 'permission_id' => 746),
            array('role_id' => 90, 'permission_id' => 751),
            array('role_id' => 90, 'permission_id' => 752),
            array('role_id' => 90, 'permission_id' => 753),
            array('role_id' => 90, 'permission_id' => 754),
            array('role_id' => 90, 'permission_id' => 755),
            array('role_id' => 90, 'permission_id' => 756),
            array('role_id' => 90, 'permission_id' => 745),
            array('role_id' => 90, 'permission_id' => 746),
            array('role_id' => 90, 'permission_id' => 769),
            array('role_id' => 57, 'permission_id' => 762),
            array('role_id' => 57, 'permission_id' => 763),
            array('role_id' => 57, 'permission_id' => 764),
            array('role_id' => 57, 'permission_id' => 765),
            array('role_id' => 57, 'permission_id' => 767),
            array('role_id' => 57, 'permission_id' => 768),
            array('role_id' => 57, 'permission_id' => 769),
            array('role_id' => 15, 'permission_id' => 747),
            array('role_id' => 15, 'permission_id' => 748),
            array('role_id' => 15, 'permission_id' => 749),
            array('role_id' => 62, 'permission_id' => 769),
            array('role_id' => 50, 'permission_id' => 769),
            array('role_id' => 8, 'permission_id' => 759),
            array('role_id' => 8, 'permission_id' => 757),
        ));
    }
}
