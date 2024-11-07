<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableOutStandingWalkInResolved extends Seeder
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
            array('id' => 168, 'name' => 'Outstanding Walk-In Shipment Resolve', 'module_id' => 8)
        ));
    }
}
