<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableTagRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 185, 'name' => 'CRM Request Tagging', 'module_id' => 18)
        ));
    }
}
