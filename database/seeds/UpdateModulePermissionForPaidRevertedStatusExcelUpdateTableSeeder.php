<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForPaidRevertedStatusExcelUpdateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 268, 'name' => 'Paid/Reverted - Excel', 'module_id' => 8)
        ));
    }
}
