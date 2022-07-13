<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForDisputeBulkStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 745, 'name' => 'Bulk Mark as In Process', 'module_id' => 1),
            array('id' => 746, 'name' => 'Bulk Mark as Resolved', 'module_id' => 1)
        ));
    }
}
