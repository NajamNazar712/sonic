<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForPendingCashCollectionReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 345, 'name' => 'Pending Cash Collection Report', 'module_id' => 9),
        ));
    }
}
