<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionTableWalkInCityListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 205, 'name' => 'Walk-In City List - View', 'module_id' => 12)
        ));
    }
}
