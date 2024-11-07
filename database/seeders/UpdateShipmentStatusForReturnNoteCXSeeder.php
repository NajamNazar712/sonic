<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusForReturnNoteCXSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 60, 'code' => 'RU-CS', 'name' => 'Return Unsuccessful for CX and Sales', 'description' => 'Shipment is refused by shipper')
        ));
    }
}
