<?php

use Illuminate\Database\Seeder;

class RiderDeliveryIncentiveRate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('rider_delivery_incentive_rates')->truncate();
        DB::table('rider_delivery_incentive_rates')->insert(array(
            array('city_id' => NULL, 'courier_type_id' => 1, 'shipment_type_id' => 1, 'shipment_weight_type_id' => 1, 'rate' =>  19, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => NULL, 'courier_type_id' => 1, 'shipment_type_id' => 1, 'shipment_weight_type_id' => 2, 'rate' =>  24, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => NULL, 'courier_type_id' => 1, 'shipment_type_id' => 1, 'shipment_weight_type_id' => 3, 'rate' =>  19, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => NULL, 'courier_type_id' => 1, 'shipment_type_id' => 2, 'shipment_weight_type_id' => 1, 'rate' =>  9, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => NULL, 'courier_type_id' => 1, 'shipment_type_id' => 2, 'shipment_weight_type_id' => 2, 'rate' =>  14, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => NULL, 'courier_type_id' => 1, 'shipment_type_id' => 2, 'shipment_weight_type_id' => 3, 'rate' =>  2, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => NULL, 'courier_type_id' => 1, 'shipment_type_id' => 3, 'shipment_weight_type_id' => 3, 'rate' =>  9, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => NULL, 'courier_type_id' => 2, 'shipment_type_id' => 1, 'shipment_weight_type_id' => 1, 'rate' =>  19, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => NULL, 'courier_type_id' => 2, 'shipment_type_id' => 1, 'shipment_weight_type_id' => 2, 'rate' =>  29, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => NULL, 'courier_type_id' => 2, 'shipment_type_id' => 2, 'shipment_weight_type_id' => 1, 'rate' =>  9, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => NULL, 'courier_type_id' => 2, 'shipment_type_id' => 2, 'shipment_weight_type_id' => 2, 'rate' =>  19, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => NULL, 'courier_type_id' => 2, 'shipment_type_id' => 2, 'shipment_weight_type_id' => 3, 'rate' =>  2, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => NULL, 'courier_type_id' => 2, 'shipment_type_id' => 3, 'shipment_weight_type_id' => 3, 'rate' =>  9, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => 223, 'courier_type_id' => 1, 'shipment_type_id' => 1, 'shipment_weight_type_id' => 1, 'rate' =>  14, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => 223, 'courier_type_id' => 1, 'shipment_type_id' => 1, 'shipment_weight_type_id' => 2, 'rate' =>  19, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => 223, 'courier_type_id' => 1, 'shipment_type_id' => 2, 'shipment_weight_type_id' => 1, 'rate' =>  5, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => 223, 'courier_type_id' => 1, 'shipment_type_id' => 2, 'shipment_weight_type_id' => 2, 'rate' =>  9, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => 223, 'courier_type_id' => 1, 'shipment_type_id' => 2, 'shipment_weight_type_id' => 3, 'rate' =>  2, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => 223, 'courier_type_id' => 1, 'shipment_type_id' => 3, 'shipment_weight_type_id' => 3, 'rate' =>  9, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => 223, 'courier_type_id' => 2, 'shipment_type_id' => 1, 'shipment_weight_type_id' => 1, 'rate' =>  14, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => 223, 'courier_type_id' => 2, 'shipment_type_id' => 1, 'shipment_weight_type_id' => 2, 'rate' =>  24, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => 223, 'courier_type_id' => 2, 'shipment_type_id' => 2, 'shipment_weight_type_id' => 1, 'rate' =>  5, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => 223, 'courier_type_id' => 2, 'shipment_type_id' => 2, 'shipment_weight_type_id' => 2, 'rate' =>  14, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => 223, 'courier_type_id' => 2, 'shipment_type_id' => 2, 'shipment_weight_type_id' => 3, 'rate' =>  2, 'created_at' => $timestamp, 'updated_at' => $timestamp),
            array('city_id' => 223, 'courier_type_id' => 2, 'shipment_type_id' => 3, 'shipment_weight_type_id' => 3, 'rate' =>  9, 'created_at' => $timestamp, 'updated_at' => $timestamp),
        ));
    }
}
