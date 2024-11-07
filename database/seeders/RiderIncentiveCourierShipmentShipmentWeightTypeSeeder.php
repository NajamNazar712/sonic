<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RiderIncentiveCourierShipmentShipmentWeightTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('rider_incentive_delivery_courier_types')->truncate();
        DB::table('rider_incentive_delivery_shipment_types')->truncate();
        DB::table('rider_incentive_delivery_shipment_weight_types')->truncate();

        DB::table('rider_incentive_delivery_courier_types')->insert(array(
            array('id' => 1, 'name' => 'Light', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => 'Heavy', 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));

        DB::table('rider_incentive_delivery_shipment_types')->insert(array(
            array('id' => 1, 'name' => 'COD', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => 'Non-COD', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'name' => 'Verification', 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));

        DB::table('rider_incentive_delivery_shipment_weight_types')->insert(array(
            array('id' => 1, 'name' => 'Light', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 2, 'name' => 'Heavy', 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('id' => 3, 'name' => 'Envelope', 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
