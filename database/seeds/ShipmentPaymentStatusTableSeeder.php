<?php

use Illuminate\Database\Seeder;

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
        ));
    }
}
