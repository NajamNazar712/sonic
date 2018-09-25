<?php

use Illuminate\Database\Seeder;

class ShipmentStatusReasonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('shipment_status_reason')->truncate();

        DB::table('shipment_status_reason')->insert(array(
			array('id' => 1, 'name' => 'Consignee Unavailable'),
			array('id' => 2, 'name' => 'Consignee Unresponsive on Phone call'),
			array('id' => 3, 'name' => 'Address Incomplete'),
			array('id' => 4, 'name' => 'Address Untraceable'),
			array('id' => 5, 'name' => 'Consignee out of city'),
			array('id' => 6, 'name' => 'Address Closed'),
			array('id' => 7, 'name' => 'Address Shifted'),
			array('id' => 8, 'name' => 'Consignee Refused'),
			array('id' => 9, 'name' => 'Issue in the COD Amount'),
			array('id' => 10, 'name' => 'Issue in the Product'),
			array('id' => 11, 'name' => 'Consigne wants to open the shipment'),
			array('id' => 12, 'name' => 'Out-of-Service Area'),
			array('id' => 13, 'name' => 'Consignee is not Responding'),
			array('id' => 14, 'name' => 'Delivery Area blocked/closed'),
			array('id' => 15, 'name' => 'Consignee wants delivery for tomorrow'),
			array('id' => 16, 'name' => 'Consignee wants delivery later'),
			array('id' => 17, 'name' => 'On Consignee\'s Request'),
			array('id' => 18, 'name' => 'On Shipper\'s Request'),
			array('id' => 19, 'name' => 'No such consignee found'),
			array('id' => 20, 'name' => 'No such order from consignee'),
			array('id' => 21, 'name' => 'Consignee is not co-operating'),
			array('id' => 22, 'name' => 'Consignee is not interested'),
			array('id' => 23, 'name' => 'Due to accident'),
			array('id' => 24, 'name' => 'Due to Shortage of Time'),
            array('id' => 25, 'name' => 'Due to Uncertain Weather'),
            array('id' => 26, 'name' => 'Due to Incorrect Destination'),
            array('id' => 27, 'name' => 'Shipment Damaged'),
            array('id' => 28, 'name' => 'Replacement not handed over'),
            array('id' => 29, 'name' => 'Due to snatching')
        ));
    }
}
