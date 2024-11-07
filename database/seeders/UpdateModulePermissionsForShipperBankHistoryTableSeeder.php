<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionsForShipperBankHistoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 257, 'name' => 'Shipper Bank History', 'module_id' => 9),
        ));
    }
}
