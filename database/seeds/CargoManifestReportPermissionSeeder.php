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
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('module_permissions')->insert(array(
            array('id' => 925, 'name' => 'Cargo Manifest Report - View', 'module_id' => 32),
        ));
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 732, 'screen_name' => 'Cargo Manifest Report', 'action' => 'View'),
        ));
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Cargo Manifest Report', 'url' => 'admin.reports.cargo_manifest.index', 'permission_id' => 925),
        ));
    }
}
