<?php

use Illuminate\Database\Seeder;

class UpdateSalesModulePermissionForSalesIncentive extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 630, 'name' => 'Sales Territory - View', 'module_id' => 14),
            array('id' => 631, 'name' => 'Sales Designation - View', 'module_id' => 14),
            array('id' => 632, 'name' => 'Sales Incentive - View', 'module_id' => 14),
            array('id' => 633, 'name' => 'Sales Incentive Report - View', 'module_id' => 9),
            array('id' => 634, 'name' => 'Consolidated Sales Incentive Report- View', 'module_id' => 9),

            array('id' => 635, 'name' => 'Sales Territory - Add/Edit', 'module_id' => 14),
            array('id' => 636, 'name' => 'Sales Designation - Add/Edit', 'module_id' => 14),
            array('id' => 637, 'name' => 'Sales Territory - Enable/Disable', 'module_id' => 14),
            array('id' => 638, 'name' => 'Sales Designation - Enable/Disable', 'module_id' => 14),

        ));
    }
}
