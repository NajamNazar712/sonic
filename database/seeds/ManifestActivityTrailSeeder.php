<?php

use Illuminate\Database\Seeder;

class ManifestActivityTrailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        DB::table('activity_trail_actions')->insert(array(
//            array('id' => 404, 'screen_name' => 'Cargo Manifest', 'action'=> 'View'),
//            array('id' => 405, 'screen_name' => 'Cargo Manifest', 'action'=> 'Excel Download'),
//            array('id' => 406, 'screen_name' => 'Cargo Manifest Short Received Report', 'action'=> 'View'),
//            array('id' => 407, 'screen_name' => 'Cargo Manifest Short Received Report', 'action'=> 'Excel Download'),
//            array('id' => 413, 'screen_name' => 'Cargo Manifest Open Box', 'action'=> 'View'),
//
//        ));
//
//        DB::table('module_permissions')->insert(array(
//            array('id' => 564, 'name' => 'Manifest - View', 'module_id' => 32),
//            array('id' => 555, 'name' => 'Cargo Manifest Short Received Report - View', 'module_id' => 32),
//        ));
        
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Cargo Vehicle Manifest > Manifest', 'url'=>'admin.cargo_manifest.index', 'permission_id' => 564),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Cargo Manifest Short Received Report', 'url'=>'admin.reports.short_received_shipments.index', 'permission_id' => 555),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Cargo Manifest Short Received Report', 'url'=>'admin.cargo_manifest.create.open_bag.index', 'permission_id' => 559),

        ));

    }
}
