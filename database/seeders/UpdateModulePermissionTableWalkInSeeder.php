<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableWalkInSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('module_permissions')->insert(array(
            array('id' => 154, 'name' => 'Walk-In', 'module_id' => 14)
        ));
    }
}
