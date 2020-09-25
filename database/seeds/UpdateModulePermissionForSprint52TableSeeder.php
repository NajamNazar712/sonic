<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForSprint52TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 387, 'name' => 'Short Received Hub Wise Report Cron Time', 'module_id' => 14),
            array('id' => 384, 'name' => 'Restrict Shipper Parcels Attempts', 'module_id' => 14),
            array('id' => 385, 'name' => 'Runner Report', 'module_id' => 14),
            array('id' => 386, 'name' => 'Runner - On Route', 'module_id' => 4)
        ));
    }
}
