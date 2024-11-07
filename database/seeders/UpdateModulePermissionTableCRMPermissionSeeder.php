<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableCRMPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 188, 'name' => 'Permission', 'module_id' => 18)
        ));
    }
}
