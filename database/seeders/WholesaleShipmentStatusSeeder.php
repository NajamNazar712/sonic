<?php

use Illuminate\Database\Seeder;

class WholesaleShipmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('wholesale_shipment_statuses')->truncate();

        DB::table('wholesale_shipment_statuses')->insert(array(
            array('id' => 1, 'name' => 'Booked'),
            array('id' => 2, 'name' => 'Arrived'),
            array('id' => 3, 'name' => 'Cancel'),
            array('id' => 4, 'name' => 'Completed'),
        ));
    }
}
