<?php

use Illuminate\Database\Seeder;

class AddShipmentStatusReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 43, 'name' => 'Hold in Operation due to Saturday Closed'),
            array('id' => 44, 'name' => 'Not attempted due to restricted area'),
            array('id' => 51, 'name' => 'Rider Unable to attempt'),
            array('id' => 22, 'name' => 'Consignee is not interested'),
            array('id' => 56, 'name' => 'Consignee not responding'),
            array('id' => 16, 'name' => 'Consignee wants delivery later'),
            array('id' => 58, 'name' => 'Consignee wants later'),
            array('id' => 10, 'name' => 'Issue in the Product'),
            array('id' => 11, 'name' => 'Consigne wants to open the shipment'),
            array('id' => 57, 'name' => 'Consignee wants open shipment'),
            array('id' => 20, 'name' => 'No such order from consignee'),
        ));
    }
}
