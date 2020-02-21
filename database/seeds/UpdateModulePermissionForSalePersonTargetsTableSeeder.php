<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForSalePersonTargetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 307, 'name' => 'Sales Person Targets - View', 'module_id' => 14),
            array('id' => 308, 'name' => 'Sales Person Targets History - View', 'module_id' => 14),
        ));
    }
}
