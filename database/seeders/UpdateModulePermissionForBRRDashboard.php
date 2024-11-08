<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForBRRDashboard extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 313, 'name' => 'Projection Percentage - View', 'module_id' => 14),
            array('id' => 314, 'name' => 'Projection Reasons - View', 'module_id' => 14),
            array('id' => 315, 'name' => 'Sales Dashboard', 'module_id' => 2),
            array('id' => 318, 'name' => 'Projection Shipment - View', 'module_id' => 14),
        ));
    }
}
