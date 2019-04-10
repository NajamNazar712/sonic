<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusTableInterceptRequested extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 54, 'code' => 'I-RE', 'name' => 'Intercept Requested', 'description' => 'Shipment is marked for intercept and requires assistance of the admin')
        ));
    }
}
