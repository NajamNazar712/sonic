<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateShipmentStatusReasonTableForRvSubStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status_reason')->insert(array(
            array('id' => 87, 'name' => 'Duplicate Order'),
            array('id' => 88, 'name' => 'Number Not Pertain to Consignee'),
        ));
    }
}
