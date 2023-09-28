<?php

use Illuminate\Database\Seeder;

class ModulePermissionForBulkHistoryCall extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 907, 'name' => 'Bulk Call History For RVR', 'module_id' => 7),
        ));
    }
}
