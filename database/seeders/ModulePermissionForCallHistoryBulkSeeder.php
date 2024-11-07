<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulePermissionForCallHistoryBulkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 906, 'name' => 'Return - Call History Bulk', 'module_id' => 7),
        ));
    }
}
