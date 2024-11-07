<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForERF extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 506, 'name' => 'ERF - View', 'module_id' => 28)
        ));
    }
}
