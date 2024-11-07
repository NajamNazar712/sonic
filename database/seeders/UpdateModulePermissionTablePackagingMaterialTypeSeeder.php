<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTablePackagingMaterialTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('module_permissions')->insert(array(
            array('id' => 214, 'name' => 'Type - View', 'module_id' => 10),
            array('id' => 215, 'name' => 'Type - Add/Edit', 'module_id' => 10),
            array('id' => 216, 'name' => 'Type - Enable/Disable', 'module_id' => 10)
        ));
    }
}
