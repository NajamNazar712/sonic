<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForManifestOpenBag extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 559, 'name' => 'Cargo Manifest Open Bag - View', 'module_id' => 32),
        ));
    }
}
