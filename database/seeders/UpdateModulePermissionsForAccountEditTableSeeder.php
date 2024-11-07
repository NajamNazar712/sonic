<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionsForAccountEditTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 255, 'name' => 'Account Edit', 'module_id' => 9),
        ));
    }
}
