<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForInvoiceAdjustmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 821, 'name' => 'Add Adjustment Against Invoice - Action', 'module_id' => 8),
        ));
    }
}
