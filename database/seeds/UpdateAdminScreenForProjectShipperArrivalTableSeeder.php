<?php

use Illuminate\Database\Seeder;

class UpdateAdminScreenForProjectShipperArrivalTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings  >  Project Arrival Shippers', 'url'=>'admin.settings.project_arrival_shippers.index', 'permission_id' => 828));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'First Mile  >  Project Shipper Arrival of Shipments', 'url'=>'admin.v2_pickups.arrival.project_shippers.index', 'permission_id' => 830));
    }
}
