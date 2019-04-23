<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableInProcessBulkCloseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 202, 'name' => 'In-Process - Close Request', 'module_id' => 18)
        ));
    }
}
