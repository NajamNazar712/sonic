<?php

use Illuminate\Database\Seeder;

class UpdateShipmentStatusTableForConsolidationShipmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 58, 'code' => 'S-WC', 'name' => 'Shipment - Waiting For Consolidation', 'description' => 'The shipment is reached at destination but the rest of the consolidation shipments are yet to be received'),
            array('id' => 59, 'code' => 'S-C', 'name' => 'Shipment - Consolidated', 'description' => 'All the shipments added in the consolidation are reached at destination and ready for delivery')
        ));
    }
}
