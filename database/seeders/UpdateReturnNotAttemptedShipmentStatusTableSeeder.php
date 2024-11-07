<?php

use Illuminate\Database\Seeder;

class UpdateReturnNotAttemptedShipmentStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 47, 'code' => 'R-NA', 'name' => 'Return - Not Attempted', 'description' => 'Shipment was dispatched for return but not attempted on route'),
            array('id' => 48, 'code' => 'R-OH', 'name' => 'Return - On Hold', 'description' => 'Shipment was attempted for return but is held by the operation'),
        ));
    }
}
