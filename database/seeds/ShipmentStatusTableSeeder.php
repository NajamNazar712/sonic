<?php

use Illuminate\Database\Seeder;

class ShipmentStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('shipment_status')->truncate();

        DB::table('shipment_status')->insert(array(
			array('id' => 1, 'code' => 'S-BK', 'name' => 'Shipment - Booked', 'description' => 'The order information is received and shipment is booked with TRAX'),
			array('id' => 2, 'code' => 'S-AOC', 'name' => 'Shipment - Arrived at Origin Center', 'description' => 'Shipment is received at TRAX premises and is ready to send to destination'),
			array('id' => 3, 'code' => 'S-IT', 'name' => 'Shipment - In Transit', 'description' => 'Shipment is on forwarded towards the destination'),
			array('id' => 4, 'code' => 'S-ADC', 'name' => 'Shipment - Arrived at Destination Center', 'description' => 'Shipment is received at the destination office of TRAX'),
			array('id' => 5, 'code' => 'S-OD', 'name' => 'Shipment - Out for Delivery', 'description' => 'Shipment is now dispatched for delivery'),
			array('id' => 6, 'code' => 'S-NA', 'name' => 'Shipment - Not Attempted', 'description' => 'Shipment was dispatched for delivery but not attempted on route'),
			array('id' => 7, 'code' => 'S-AF', 'name' => 'Shipment - Attempt Failed', 'description' => 'Shipment was attempted for delivery but could not be delivered'),
			array('id' => 8, 'code' => 'S-OH', 'name' => 'Shipment - On Hold', 'description' => 'Shipment was attempted for delivery but is held by the consignee'),
			array('id' => 9, 'code' => 'S-NSA', 'name' => 'Shipment - Non-Service Area', 'description' => 'Shipment is out of the service boundaries of the destination office'),
			array('id' => 10, 'code' => 'S-MR', 'name' => 'Shipment - Misroute', 'description' => 'Shipment is sent to wrong destination'),
			array('id' => 11, 'code' => 'S-PD', 'name' => 'Shipment - Pending Decision', 'description' => 'Shipment is in pending condition and requires assistance of the consignee and the shipper'),
			array('id' => 12, 'code' => 'S-RA', 'name' => 'Shipment - Re-Attempt', 'description' => 'Shipment will now be re-attempted upon confirmation'),
			array('id' => 13, 'code' => 'S-DE', 'name' => 'Shipment - Delivered', 'description' => 'Shipment is delivered to the consignee'),
			array('id' => 14, 'code' => 'S-PD', 'name' => 'Shipment - Partial Delivered', 'description' => 'Some contents of the shipment are delivered and the rest are returned to the shipper'),
			array('id' => 15, 'code' => 'S-SCN', 'name' => 'Shipment - Self Collection', 'description' => 'Consignee will collect the shipment from the destination office of TRAX'),
			array('id' => 16, 'code' => 'S-SCD', 'name' => 'Shipment - Self Collected', 'description' => 'Consignee has self collected the shipment from the destination office of TRAX'),
			array('id' => 17, 'code' => 'S-CA', 'name' => 'Shipment - Cancelled', 'description' => 'Shipment is cancelled from the shipper\'s end'),
			array('id' => 18, 'code' => 'S-LO', 'name' => 'Shipment - Lost', 'description' => 'Shipment is Lost '),
			array('id' => 19, 'code' => 'S-RB', 'name' => 'Shipment - Re-Booked', 'description' => 'Shipment is re-booked for the correct destination'),
			array('id' => 20, 'code' => 'RT-CO', 'name' => 'Return - Confirm', 'description' => 'Shipment is confirmed by the shipper to be returned'),
			array('id' => 21, 'code' => 'RT-IT', 'name' => 'Return - In Transit', 'description' => 'Return shipment is sent back to the origin office for return'),
			array('id' => 22, 'code' => 'RT-AOC', 'name' => 'Return - Arrived at Origin Center', 'description' => 'Return shipment is received at the origin office for return'),
			array('id' => 23, 'code' => 'RT-DP', 'name' => 'Return - Dispatched', 'description' => 'Return shipment is dispatched to be sent back to the shipper'),
			array('id' => 24, 'code' => 'RT-AF', 'name' => 'Return - Attempt Failed', 'description' => 'Return shipment was attempted for delivery but could not be delivered'),
			array('id' => 25, 'code' => 'RT-DS', 'name' => 'Return - Delivered to Shipper', 'description' => 'Return shipment is sent back to the shipper'),
			array('id' => 26, 'code' => 'RP-AOC', 'name' => 'Replacement - Arrived at Origin Center', 'description' => 'Replacement shipment is received at the origin office for return'),
			array('id' => 27, 'code' => 'RP-DP', 'name' => 'Replacement - Dispatched', 'description' => 'Replacement shipment is dispatched to be sent back to the shipper'),
			array('id' => 28, 'code' => 'RP-AF', 'name' => 'Replacement - Attempt Failed', 'description' => 'Replacement shipment was attempted for delivery but could not be delivered'),
			array('id' => 29, 'code' => 'RP-EX', 'name' => 'Replacement - Exchanged', 'description' => 'Shipment is delivered to the consignee and its replacement is collected from him/her'),
			array('id' => 30, 'code' => 'RP-DS', 'name' => 'Replacement - Delivered to Shipper', 'description' => 'Replacement shipment is sent back to the shipper'),
			array('id' => 31, 'code' => 'P-PR', 'name' => 'Payment - Processed', 'description' => 'Payment for the shipment is processed by TRAX'),
			array('id' => 32, 'code' => 'P-PA', 'name' => 'Payment - Paid', 'description' => 'Payment reimbursed to the shipper'),
			array('id' => 33, 'code' => 'P-RE', 'name' => 'Payment - Reverted', 'description' => 'Payment is reverted from the bank\'s end'),
			array('id' => 34, 'code' => 'P-AD', 'name' => 'Payment - Adjusted', 'description' => 'Shipment is wrongly paid and the amount is adjusted in next payment'),
			array('id' => 35, 'code' => 'P-CD', 'name' => 'Payment - Charges Deducted', 'description' => 'Charges have been deducted of the Return Shipment'),
            array('id' => 36, 'code' => 'S-RE', 'name' => 'Shipment - Rider Exchange', 'description' => 'Shipment not pertain\'s to assigned rider\'s route')
        ));
    }
}
