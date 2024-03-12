<?php

use Illuminate\Database\Seeder;

class CargoManifestReportPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 933, 'name' => 'Cargo Manifest Report - View', 'module_id' => 9),
        ));
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 732, 'screen_name' => 'Cargo Manifest Report', 'action' => 'View'),
        ));

    }
}
