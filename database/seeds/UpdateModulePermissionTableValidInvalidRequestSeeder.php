<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableValidInvalidRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 184, 'name' => 'Valid/Invalid', 'module_id' => 18)
        ));
    }
}
