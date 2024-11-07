<?php

use Illuminate\Database\Seeder;

class ShipmentPickupStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_pickup_status')->truncate();

        DB::table('shipment_pickup_status')->insert(array(
			array('id' => 1, 'name' => 'Request Received'),
			array('id' => 2, 'name' => 'Request Assigned to Note'),
			array('id' => 3, 'name' => 'Note Dispatched'),
			array('id' => 4, 'name' => 'Note Received'),
			array('id' => 5, 'name' => 'Note Completed'),
			array('id' => 6, 'name' => 'Request Cancelled'),
			array('id' => 7, 'name' => 'Note Cancelled')
        ));
    }
}
