<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRetailRates extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 584 , 'name' => 'Retail Standard Rates - View', 'module_id' => 26),
            array('id' => 585 , 'name' => 'Retail International Rate Upload - View', 'module_id' => 26)
        ));
    }
}
