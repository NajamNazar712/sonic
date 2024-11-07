<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableCancelledShipmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert(array(
            array('id' => 15, 'name' => 'Cancelled Shipments')
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 117, 'name' => 'View', 'module_id' => 15),
            array('id' => 118, 'name' => 'Revert', 'module_id' => 15)
        ));
    }
}
