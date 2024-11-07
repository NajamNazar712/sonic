<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateModulePermissionForReportsSprint34TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 300, 'name' => 'Route Distribution Summary - View', 'module_id' => 9),
            array('id' => 301, 'name' => 'Arrived At Destination VS Out For Delivery VS Received - View', 'module_id' => 9),
        ));
    }
}
