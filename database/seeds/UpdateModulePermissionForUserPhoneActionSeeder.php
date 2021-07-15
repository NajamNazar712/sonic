<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForUserPhoneActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 542, 'name' => 'Admin Phone Update - Action', 'module_id' => 11),
        ));
    }
}
