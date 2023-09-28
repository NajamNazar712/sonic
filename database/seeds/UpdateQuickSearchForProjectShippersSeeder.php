<?php

use Illuminate\Database\Seeder;

class UpdateQuickSearchForProjectShippersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'First Mile > Pickups > Arrival Projects/Shipper of Shipments', 'url'=>'admin.v2_pickups.arrival.project_shippers.index', 'permission_id' => 830)
        );
    }
}
