<?php

use Illuminate\Database\Seeder;

class UpdatePermissionModuleForShipperDefaultWeightTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 326, 'name' => 'Shipper Default Weight', 'module_id' => 14)
        ));
    }
}
a