<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShipmentStatusScreenLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $conditions = [
            ['shipment_status_id' => 2, 'screen_location_id' => 1],
            ['shipment_status_id' => 3, 'screen_location_id' => 2],
            ['shipment_status_id' => 4, 'screen_location_id' => 20],
            ['shipment_status_id' => 4, 'screen_location_id' => 21],
            ['shipment_status_id' => 5, 'screen_location_id' => 4],
            ['shipment_status_id' => 11, 'screen_location_id' => 3],
            ['shipment_status_id' => 11, 'screen_location_id' => 10],
            ['shipment_status_id' => 11, 'screen_location_id' => 20],
            ['shipment_status_id' => 11, 'screen_location_id' => 21],
            ['shipment_status_id' => 21, 'screen_location_id' => 2],
            ['shipment_status_id' => 22, 'screen_location_id' => 20],
            ['shipment_status_id' => 23, 'screen_location_id' => 7],
            ['shipment_status_id' => 26, 'screen_location_id' => 2],
            ['shipment_status_id' => 27, 'screen_location_id' => 20],
            ['shipment_status_id' => 28, 'screen_location_id' => 7],
            ['shipment_status_id' => 32, 'screen_location_id' => 2],
            ['shipment_status_id' => 33, 'screen_location_id' => 20],
            ['shipment_status_id' => 34, 'screen_location_id' => 7],
            ['shipment_status_id' => 53, 'screen_location_id' => 31],
        ];
        DB::table('shipment_status_screen_locations')->insert($conditions);


    }
}
