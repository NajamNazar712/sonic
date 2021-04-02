<?php

use Illuminate\Database\Seeder;

class UpdateSearchSonicSeederForMasterCargoReports extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports  > In Transit Report Bag Wise', 'url'=>'admin.reports.master_cargo.bag.in_transit.index', 'permission_id' => 421));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports  > Master Cargo Short Received Shipments', 'url'=>'admin.reports.master_cargo.short_received_shipments.index', 'permission_id' => 421));

    }
}
