<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HandoverShipmentStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('handover_shipment_statuses')->truncate();

        DB::table('handover_shipment_statuses')->insert(array(
            array('id' => 1, 'name' => 'Forwarded'),
            array('id' => 2, 'name' => 'Received'),
            array('id' => 3, 'name' => 'Delivered')
            
        ));
    }
}
