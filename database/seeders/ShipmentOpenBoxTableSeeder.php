<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class ShipmentOpenBoxTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('shipment_open_box_statuses')->truncate();

        DB::table('shipment_open_box_statuses')->insert(array(
            array('id' => 1, 'name' => 'Cargo Creation', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => 'Cargo Receiving', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'name' => 'Delivery Note Creation', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 4, 'name' => 'Delivery Note Updation', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 5, 'name' => 'Delivery Note Verification', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 6, 'name' => 'Return Note Creation', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 7, 'name' => 'Return Note Updation', 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
