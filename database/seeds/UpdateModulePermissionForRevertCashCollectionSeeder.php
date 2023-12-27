<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForRevertCashCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 913, 'name' => 'Cash Collect Revert', 'module_id' => 6)
        ));
    }
}
