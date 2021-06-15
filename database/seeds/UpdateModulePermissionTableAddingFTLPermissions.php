<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableAddingFTLPermissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 511, 'name' => 'FTL Request - View', 'module_id' => 30),
            array('id' => 512, 'name' => 'FTL Request - Add', 'module_id' => 30),
            array('id' => 513, 'name' => 'FTL Request Detail - View', 'module_id' => 30),
            array('id' => 514, 'name' => 'FTL Request Charges - Update', 'module_id' => 30),
            array('id' => 515, 'name' => 'FTL Request Charges - Approve/Reject', 'module_id' => 30),
            array('id' => 516, 'name' => 'FTL Request Walk-in Booking', 'module_id' => 30),
        ));
    }
}
