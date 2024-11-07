<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableAddRiderPickupAndRiperPickupActionLog extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 271, 'name' => 'Rider - View', 'module_id' => 3),
            array('id' => 272, 'name' => 'Rider Action Log - View', 'module_id' => 3)
        ));
    }
}
