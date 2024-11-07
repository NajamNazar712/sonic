<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableInterceptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 193, 'name' => 'Intercept Request - View', 'module_id' => 6),
            array('id' => 194, 'name' => 'Intercept Request - Approve', 'module_id' => 6),
            array('id' => 195, 'name' => 'Intercept Request - Reject', 'module_id' => 6),
        ));
    }
}
