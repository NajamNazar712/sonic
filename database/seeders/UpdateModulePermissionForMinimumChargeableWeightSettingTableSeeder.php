<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForMinimumChargeableWeightSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 303, 'name' => 'Minimum Chargeable Weight - View', 'module_id' => 14)
        ));
    }
}
