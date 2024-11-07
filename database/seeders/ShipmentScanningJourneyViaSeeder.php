<?php

use Illuminate\Database\Seeder;

class ShipmentScanningJourneyViaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_scanning_journey_vias')->truncate();

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('shipment_scanning_journey_vias')->insert(array(
            array('id' => 1, 'name' => 'Web', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => 'App', 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
