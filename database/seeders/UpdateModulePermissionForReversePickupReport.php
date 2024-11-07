<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForReversePickupReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 624, 'name' => 'Reverse Pickup', 'module_id' => 9)
        ));
    }
}
