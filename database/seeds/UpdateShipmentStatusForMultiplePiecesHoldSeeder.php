<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusForMultiplePiecesHoldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 62, 'code' => 'HAO-S', 'name' => 'Shipment - Multiple Pieces Hold', 'description' => 'Shipment is being held due to short pieces received')
        ));
    }
}
