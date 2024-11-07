<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateSearchSonicSeederForSupport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support  >  Telenor  >  Bulk Arrival', 'url'=>'admin.telenor.arrival.index', 'permission_id' => 421));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support  >  Telenor  >  Bulk Order ID', 'url'=>'admin.telenor.order_id.index', 'permission_id' => 421));
    }
}
