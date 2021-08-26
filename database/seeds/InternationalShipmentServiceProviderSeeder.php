<?php

use Illuminate\Database\Seeder;

class InternationalShipmentServiceProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('international_shipment_service_providers')->truncate();
        DB::table('international_shipment_service_providers')->insert(array(
            array('id' => 1, 'name'=>'DHL'),
            array('id' => 2, 'name'=>'FedEx'),
            array('id' => 3, 'name'=>'Global Logistics'),

        ));
    }
}
