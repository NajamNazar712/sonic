<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForHandover extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 339, 'name' => 'Handover', 'module_id' => 22)
        ));
    }
}
