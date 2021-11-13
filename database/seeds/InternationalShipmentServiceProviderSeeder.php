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
            array('id' => 1, 'name'=>'DHL', 'code' => 'T001'),
            array('id' => 2, 'name'=>'FedEx', 'code' => 'T002'),
            array('id' => 3, 'name'=>'UPS', 'code' => 'T003'),
            array('id' => 4, 'name'=>'Aramex', 'code' => 'T004'),
            array('id' => 5, 'name'=>'SkyNet', 'code' => 'T005'),
            array('id' => 6, 'name'=>'Nice Express AE', 'code' => 'T006'),
            array('id' => 7, 'name'=>'Others', 'code' => 'T007'),

        ));
    }
}
