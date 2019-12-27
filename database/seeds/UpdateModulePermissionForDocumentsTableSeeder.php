<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateModulePermissionForDocumentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 257, 'name' => 'Upload Documents', 'module_id' => 2),
            array('id' => 258, 'name' => 'Approve/Reject Documents', 'module_id' => 2)
        ));
    }
}
