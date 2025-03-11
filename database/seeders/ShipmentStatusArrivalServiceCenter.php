<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ShipmentStatusArrivalServiceCenter extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        {
            DB::table('shipment_status')->insert(array(
                array('id' => 61, 'code' => 'S-ASC', 'name' => 'Shipment - Arrival Service Center', 'description' => 'Shipment  has been arrived at Service Center ')
            ));
        }
    }
}
