<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForUpdateRetailShipperAccountTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 486, 'name' => 'Retail Account - Edit', 'module_id' => 26),
        ));
    }
}
