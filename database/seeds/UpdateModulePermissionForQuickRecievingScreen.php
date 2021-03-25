<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForQuickRecievingScreen extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 464, 'name' => 'Quick Receiving - View', 'module_id' => 6)
        ));
    }
}
