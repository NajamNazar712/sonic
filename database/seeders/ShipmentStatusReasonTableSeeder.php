<?php

namespace Database\Seeders;

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
            array('id' => 29, 'name' => 'Due to snatching'),
            array('id' => 30, 'name' => 'Shipper Unavailable'),
            array('id' => 31, 'name' => 'Shipper’s mistake in booking'),
            array('id' => 32, 'name' => 'Consignee did not handover the shipment due to product or shipper issue'),
            array('id' => 33, 'name' => 'Consignee wants to receive both parcel and agrees with shipper to pay for both'),
            array('id' => 34, 'name' => 'Non-Service Area'),
            array('id' => 35, 'name' => 'Delivery Stopped'),
            array('id' => 36, 'name' => 'Snatched by consignee'),
            array('id' => 37, 'name' => 'Misplaced by rider'),
            array('id' => 38, 'name' => 'Consignee Refused'),
            array('id' => 39, 'name' => 'Delay in Delivery'),
            array('id' => 40, 'name' => 'Wrong destination'),
            array('id' => 41, 'name' => 'Purchased from other vendor'),
            array('id' => 42, 'name' => 'Refused after opening the shipment'),
            array('id' => 43, 'name' => 'Hold in Operation due to Saturday Closed'),
            array('id' => 44, 'name' => 'Not attempted due to restricted area'),
            array('id' => 45, 'name' => 'No one came for Self-Collection'),
            array('id' => 46, 'name' => 'Address Issue'),
            array('id' => 47, 'name' => 'Issue in the product'),
            array('id' => 48, 'name' => 'Shipment Damage'),
            array('id' => 49, 'name' => 'No such order/consignee'),
            array('id' => 50, 'name' => 'NSA / OSA parcel'),
            array('id' => 51, 'name' => 'Rider Unable to attempt'),
            array('id' => 52, 'name' => 'Consignee Unavailable'),
            array('id' => 53, 'name' => 'As per Shipper Request'),
            array('id' => 54, 'name' => 'Wants to Open'),
            array('id' => 55, 'name' => 'Consignee Unresponsive'),
        ));
    }
}
