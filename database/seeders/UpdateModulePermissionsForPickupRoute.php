<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionsForPickupRoute extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 406, 'name' => 'Pickup Route', 'module_id' => 3)
        ));
    }
}
