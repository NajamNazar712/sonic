<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForLeadBulkStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 678, 'name' => 'Bulk Status Button', 'module_id' => 25),
        ));
    }
}
