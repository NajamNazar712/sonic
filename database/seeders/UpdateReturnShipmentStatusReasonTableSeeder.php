<?php

use Illuminate\Database\Seeder;

class UpdateReturnShipmentStatusReasonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 30, 'name' => 'Shipper Unavailable'),
        ));
    }
}
