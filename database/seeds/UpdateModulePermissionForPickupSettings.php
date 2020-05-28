<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForPickupSettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 338, 'name' => 'Pickup Settings', 'module_id' => 14)
        ));
    }
}
