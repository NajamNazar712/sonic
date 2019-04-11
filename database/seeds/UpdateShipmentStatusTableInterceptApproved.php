<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusTableInterceptApproved extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 55, 'code' => 'I-AP', 'name' => 'Intercept Approved', 'description' => 'Shipment is marked Approved for intercept')
        ));
    }
}
