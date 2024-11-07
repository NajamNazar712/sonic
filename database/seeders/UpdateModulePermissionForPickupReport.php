<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForPickupReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 337, 'name' => 'Pickup Report', 'module_id' => 9)
        ));
    }
}
