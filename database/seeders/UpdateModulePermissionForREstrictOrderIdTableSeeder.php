<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForREstrictOrderIdTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 619, 'name' => 'Restrict Order ID', 'module_id' => 2)
        ));
    }
}
