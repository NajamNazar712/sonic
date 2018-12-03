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
            array('id' => 49, 'code' => 'S-MP', 'name' => 'Shipment - Misroute-forwarded', 'description' => 'Misroute Shipment was updated to new destination'),
        ));
    }
}
