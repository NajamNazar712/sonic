<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForCRFTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 244, 'name' => 'CRF - View', 'module_id' => 2),
        ));
    }
}
