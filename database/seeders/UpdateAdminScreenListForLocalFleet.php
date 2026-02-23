<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateAdminScreenListForLocalFleet extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Local Fleet Management > Vehicle', 'url' => 'admin.cargo.supply_chain.local_fleet.vehicle.index', 'permission_id' => 1062),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Local Fleet Management > Vehicle Trips', 'url' => 'admin.cargo.supply_chain.local_fleet.vehicle.trips.index', 'permission_id' => 1068),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Local Fleet Management > Daily Activity Report', 'url' => 'admin.cargo.supply_chain.local_fleet.vehicle.consolidated_trips.index', 'permission_id' => 1069),
        ));
    }
}
