<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateAdminSearchSonicSeederForPickupRoutes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'First Mile  >  V2 Pickups  >  Pickup Route', 'url'=>'admin.v2_pickups.pickup_route.index', 'permission_id' => 406));
    }
}
