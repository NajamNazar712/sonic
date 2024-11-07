<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForActivityTrail extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 471, 'name' => 'Activity Trail - View', 'module_id' => 27),
        ));
    }
}
