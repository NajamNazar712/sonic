<?php

use Illuminate\Database\Seeder;

class AddModulePermissionForCRMSmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 914, 'name' => 'CRM SMS - View', 'module_id' => 14),
        
        ));
    }
}
