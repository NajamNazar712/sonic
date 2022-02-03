<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForCRMCountReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 673, 'name' => 'CRM Count Report', 'module_id' => 9)
        ));
    }
}
