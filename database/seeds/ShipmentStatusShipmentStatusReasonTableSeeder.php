<?php

use Illuminate\Database\Seeder;

class ShipmentStatusShipmentStatusReasonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('shipment_status_shipment_status_reason')->truncate();

        DB::table('shipment_status_shipment_status_reason')->insert(array(
        	array('shipment_status_id' => 7, 'shipment_status_reason_id' => 14),
			array('shipment_status_id' => 7, 'shipment_status_reason_id' => 23),
			array('shipment_status_id' => 7, 'shipment_status_reason_id' => 24),
			array('shipment_status_id' => 7, 'shipment_status_reason_id' => 25),
			array('shipment_status_id' => 8, 'shipment_status_reason_id' => 1),
			array('shipment_status_id' => 8, 'shipment_status_reason_id' => 2),
			array('shipment_status_id' => 8, 'shipment_status_reason_id' => 3),
			array('shipment_status_id' => 8, 'shipment_status_reason_id' => 4),
			array('shipment_status_id' => 8, 'shipment_status_reason_id' => 5),
			array('shipment_status_id' => 8, 'shipment_status_reason_id' => 6),
			array('shipment_status_id' => 8, 'shipment_status_reason_id' => 9),
			array('shipment_status_id' => 8, 'shipment_status_reason_id' => 12),
			array('shipment_status_id' => 8, 'shipment_status_reason_id' => 13),
			array('shipment_status_id' => 8, 'shipment_status_reason_id' => 19),
			array('shipment_status_id' => 8, 'shipment_status_reason_id' => 21),
			array('shipment_status_id' => 8, 'shipment_status_reason_id' => 22),
			array('shipment_status_id' => 8, 'shipment_status_reason_id' => 28),
			array('shipment_status_id' => 9, 'shipment_status_reason_id' => 15),
			array('shipment_status_id' => 9, 'shipment_status_reason_id' => 16),
			array('shipment_status_id' => 9, 'shipment_status_reason_id' => 17),
			array('shipment_status_id' => 9, 'shipment_status_reason_id' => 18),
			array('shipment_status_id' => 11, 'shipment_status_reason_id' => 7),
			array('shipment_status_id' => 11, 'shipment_status_reason_id' => 26),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 1),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 2),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 3),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 4),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 5),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 6),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 7),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 8),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 9),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 10),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 11),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 13),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 19),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 20),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 21),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 22),
			array('shipment_status_id' => 12, 'shipment_status_reason_id' => 27),
			array('shipment_status_id' => 15, 'shipment_status_reason_id' => 15),
			array('shipment_status_id' => 15, 'shipment_status_reason_id' => 16),
			array('shipment_status_id' => 15, 'shipment_status_reason_id' => 17),
			array('shipment_status_id' => 15, 'shipment_status_reason_id' => 18)
        ));
    }
}
