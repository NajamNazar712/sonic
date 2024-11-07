<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableEditCrmRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 213, 'name' => 'Edit Request', 'module_id' => 18)
        ));
    }
}
