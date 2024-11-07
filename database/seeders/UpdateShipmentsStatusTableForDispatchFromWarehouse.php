<?php

use Illuminate\Database\Seeder;

class UpdateShipmentsStatusTableForDispatchFromWarehouse extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 63, 'code' => 'D-FW', 'name' => 'Shipment - Dispatched From Warehouse', 'description' => 'Shipment is dispatched from warehouse after gate pass'),
        ));
    }
}
