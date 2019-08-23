<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableAdminInterceptRebookRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 245, 'name' => 'Intercept/Re-Book - View', 'module_id' => 7)
        ));
    }
}
