<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionsForERF extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 518, 'name' => 'Approve By HOD - Action', 'module_id' => 28),
            array('id' => 519, 'name' => 'Approve By HOD - Action', 'module_id' => 28),
            array('id' => 520, 'name' => 'Documents Approve - Action', 'module_id' => 28),
            array('id' => 521, 'name' => 'Documents View - Action', 'module_id' => 28)
        ));
    }
}
