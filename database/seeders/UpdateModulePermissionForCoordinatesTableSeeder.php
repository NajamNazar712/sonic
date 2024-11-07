<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForCoordinatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 324, 'name' => 'Support - Add Coordinates', 'module_id' => 12),
        ));
    }
}
