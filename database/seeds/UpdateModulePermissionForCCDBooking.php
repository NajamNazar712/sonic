<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForCCDBooking extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 498, 'name' => 'CCD Shipper', 'module_id' => 2)
        ));
    }
}
