<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForPendingShipmentsReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 430, 'name' => 'Confirmation Pending Shipments Report', 'module_id' => 9)
        ));
    }
}
