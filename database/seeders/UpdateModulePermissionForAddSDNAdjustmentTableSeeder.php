<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForAddSDNAdjustmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 251, 'name' => 'SDN Adjustments Add', 'module_id' => 6),
        ));
    }
}
