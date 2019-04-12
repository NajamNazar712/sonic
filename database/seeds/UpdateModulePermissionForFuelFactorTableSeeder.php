<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForFuelFactorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 189, 'name' => 'Fuel Factor', 'module_id' => 14)
        ));
    }
}
