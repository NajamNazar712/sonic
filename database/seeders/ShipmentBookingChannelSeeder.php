<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;
class ShipmentBookingChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('channels')->truncate();

        DB::table('channels')->insert(array(
            array('id' => 1, 'name' => 'Sonic', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => 'Mobile-App', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'name' => 'Shopify', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 4, 'name' => 'Wordpress', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 5, 'name' => 'API', 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
