<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionCRMUploadImage extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 623, 'name' => 'Crm Delete Image', 'module_id' => 18),
        ));
    }
}
