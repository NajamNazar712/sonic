<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForInternationalEconomyRates extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 528, 'name' => 'International Economy Rates - Add', 'module_id' => 2),
            array('id' => 529, 'name' => 'International Economy Rates - Approve', 'module_id' => 2),
            array('id' => 530, 'name' => 'International Economy Rates - View', 'module_id' => 2),
        ));
    }
}
