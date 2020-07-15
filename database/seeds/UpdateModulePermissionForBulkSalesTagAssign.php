<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForBulkSalesTagAssign extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 361, 'name' => 'Bulk Sales Tag Assign', 'module_id' => 2)
        ));
    }
}
