<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableRatesApproved extends Seeder
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
            array('id' => 140, 'name' => 'Active - Authorize Rates', 'module_id' => 2)
        ));
    }
}
