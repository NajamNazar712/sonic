<?php

use Illuminate\Database\Seeder;

class V2PickupReportLegendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('v2_pickup_report_legends')->truncate();
        DB::table('v2_pickup_report_legends')->insert(array(
            array('id' => 1, 'name' => 'Pickup Request Picked, Shipment Difference < 10%', 'color' => '#228B22'),
            array('id' => 2, 'name' => 'Pickup Request Picked, Shipment Difference > 10%', 'color' => '#98FB98'),
            array('id' => 3, 'name' => 'Pickup Request Attempted & Not Picked', 'color' => '#FFDEAD'),
            array('id' => 4, 'name' => 'Pickup Request Cancelled', 'color' => '#D3D3D3'),
            array('id' => 5, 'name' => 'Pickup Request Attempt Failed', 'color' => '#FA8072')
        ));
    }
}
