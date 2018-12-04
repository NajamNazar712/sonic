<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusForMisroutedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 49, 'code' => 'S-MP', 'name' => 'Shipment - Misroute Forwarded', 'description' => 'Shipment is ready to forward to new destination'),
        ));
    }
}
