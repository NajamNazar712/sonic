<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableBulkCloseSeederNewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 787, 'name' => 'Bulk Valid/Invalid - Resolved/Closed', 'module_id' => 18)
        ));
    }
}
