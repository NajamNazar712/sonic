<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableForSprint37Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 222, 'name' => 'Re-Assign Rider', 'module_id' => 4)
        ));
    }
}
