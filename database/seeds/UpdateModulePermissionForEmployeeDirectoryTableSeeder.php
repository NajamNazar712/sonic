<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForEmployeeDirectoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 463, 'name' => 'Employee Directory - View', 'module_id' => 11),
        ));
    }
}
