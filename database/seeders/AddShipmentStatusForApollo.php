<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class AddShipmentStatusForApollo extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_status')->insert(array(
            array('id' => 151, 'code' => 'S-RJ','name' => 'Shipment - Received at Junction','description' => 'Shipment - Received at Junction'),
            array('id' => 152, 'code' => 'S-OF','name' => 'Shipment - Onward Forwarded','description' => 'Shipment - Onward Forwarded'),
        ));
    }
}
