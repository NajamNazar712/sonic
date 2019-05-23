<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableAssignAgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 179, 'name' => 'Assign Agent', 'module_id' => 18)
        ));
    }
}
