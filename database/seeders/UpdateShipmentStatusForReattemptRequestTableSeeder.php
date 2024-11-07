<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusForReattemptRequestTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestampts = \Carbon\Carbon::now();
        DB::table('shipment_status')->insert(array(
            array('id' => 52, 'code' => 'S-RR', 'name' => 'Shipment - Re-Attempt Requested', 'description' => 'Shipment is requested to be re-attempted by the shipper', 'created_at' => $timestampts, 'updated_at' => $timestampts),
        ));
    }
}
