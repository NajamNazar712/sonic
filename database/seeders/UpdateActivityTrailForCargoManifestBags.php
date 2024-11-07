<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateActivityTrailForCargoManifestBags extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 398, 'screen_name' => 'Bag (Cargo Manifest)', 'action'=> 'View'),
            array('id' => 399, 'screen_name' => 'Bag (Cargo Manifest)', 'action'=> 'Excel Download'),
            array('id' => 400, 'screen_name' => 'Create Bag (Cargo Manifest)', 'action'=> 'View'),
            array('id' => 401, 'screen_name' => 'Bag History (Cargo Manifest)', 'action'=> 'View'),
            array('id' => 402, 'screen_name' => 'Bag History(Cargo Manifest)', 'action'=> 'Excel Download'),
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 545, 'name' => 'Pending - View', 'module_id' => 32),
            array('id' => 546, 'name' => 'Create - View', 'module_id' => 32),
            array('id' => 547, 'name' => 'History - View', 'module_id' => 32)
        ));


        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Cargo Vehicle Manifest > Bag > Pending', 'url'=>'admin.cargo_manifest.bags.pending.index', 'permission_id' => 545),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Cargo Vehicle Manifest > Bag > Create', 'url'=>'admin.cargo_manifest.bags.create.index', 'permission_id' => 546),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Cargo Vehicle Manifest > Bag > History', 'url'=>'admin.cargo_manifest.bags.history.index', 'permission_id' => 547),
        ));
        
    }
}
