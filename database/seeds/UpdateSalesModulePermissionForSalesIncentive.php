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
            array('id' => 610, 'name' => 'Create Territory - View', 'module_id' => 14),
            array('id' => 611, 'name' => 'Designation - View', 'module_id' => 14),
            array('id' => 612, 'name' => 'Sales Incentive - View', 'module_id' => 14),
            array('id' => 613, 'name' => 'Sales Incentive Report - View', 'module_id' => 9),
            array('id' => 614, 'name' => 'Consolidated Sales Incentive Report- View', 'module_id' => 9),

            array('id' => 626, 'name' => 'Sales Territory - Add/Edit', 'module_id' => 14),
            array('id' => 627, 'name' => 'Sales Designation - Add/Edit', 'module_id' => 14),
            array('id' => 628, 'name' => 'Sales Territory - Enable/Disable', 'module_id' => 14),
            array('id' => 629, 'name' => 'Sales Designation - Enable/Disable', 'module_id' => 14),

        ));
    }
}
