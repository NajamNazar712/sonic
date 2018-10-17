<?php

use Illuminate\Database\Seeder;

class UpdateModulePermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 109, 'name' => 'Profile - View', 'module_id' => 2),
            array('id' => 110, 'name' => 'Profile - Edit Personal Information', 'module_id' => 2),
            array('id' => 111, 'name' => 'Profile - Edit Bank Information', 'module_id' => 2),
        ));
    }
}
