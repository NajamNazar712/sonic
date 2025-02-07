<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class ShipmentPaymentStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('shipment_payment_status')->truncate();

        DB::table('shipment_payment_status')->insert(array(
			array('id' => 1, 'code' => 'P-PR', 'name' => 'Payment - Processed', 'description' => 'Payment for the shipment is processed by TRAX'),
			array('id' => 2, 'code' => 'P-RE', 'name' => 'Payment - Reverted', 'description' => 'Payment is reverted from the bank\'s end'),
			array('id' => 3, 'code' => 'P-PA', 'name' => 'Payment - Paid', 'description' => 'Payment reimbursed to the shipper'),
			array('id' => 4, 'code' => 'P-AD', 'name' => 'Payment - Adjusted', 'description' => 'Shipment is wrongly paid and the amount is adjusted in next payment'),
			array('id' => 5, 'code' => 'C-PR', 'name' => 'Charges - Processed', 'description' => 'Charges for the shipment is processed by TRAX'),
			array('id' => 6, 'code' => 'C-RE', 'name' => 'Charges - Reverted', 'description' => 'Charges is reverted from the bank\'s end'),
			array('id' => 7, 'code' => 'C-DE', 'name' => 'Charges - Deducted', 'description' => 'Charges have been deducted of the Return Shipment'),
			array('id' => 8, 'code' => 'P-AR', 'name' => 'Payment - Arrival Processed', 'description' => 'Payment for the shipment is arrived and processed by TRAX'),
			array('id' => 9, 'code' => 'P-AP', 'name' => 'Payment - Arrival Paid', 'description' => 'Payment for the shipment is arrived and paid by TRAX'),
            array('id' => 10, 'code' => 'AC-R', 'name' => 'Charges - Arrival Processed', 'description' => 'Arrival Charges for the shipment is processed by TRAX'),
            array('id' => 11, 'code' => 'AC-RE', 'name' => 'Charges - Arrival Reverted', 'description' => 'Arrival Charges is reverted from the bank\'s end'),
            array('id' => 12, 'code' => 'AC-DE', 'name' => 'Charges - Arrival Deducted', 'description' => 'Arrival Charges have been deducted of the Return Shipment'),
        ));
    }
}
