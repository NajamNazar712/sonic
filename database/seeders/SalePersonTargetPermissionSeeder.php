<?php

use Illuminate\Database\Seeder;

class SalePersonTargetPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 790, 'name' => 'Sale Person Target Delete - Action', 'module_id' => 33),
        ));

        DB::table('admin_role_module_permissions')->insert(array(
            array('role_id' => 44, 'permission_id' => 790),
        ));
    }
}
