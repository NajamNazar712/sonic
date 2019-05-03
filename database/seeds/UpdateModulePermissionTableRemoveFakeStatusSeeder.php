<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableRemoveFakeStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 203, 'name' => 'Remove Fake Status', 'module_id' => 6)
        ));
    }
}
