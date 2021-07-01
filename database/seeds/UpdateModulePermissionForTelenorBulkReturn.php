<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForTelenorBulkReturn extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 525, 'name' => 'Telenor Bulk Return - View', 'module_id' => 6),

        ));
    }
}
