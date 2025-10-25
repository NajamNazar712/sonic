<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateScreenLocationForArrivalServiceCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_scanning_screen_locations')->insert(array(
            array('id' => 32, 'name' => 'Retail - Arrival Service Center'),
            array('id' => 33, 'name' => 'Mobile App - Transfer Note Create'),
            array('id' => 34, 'name' => 'Update Rider Transfer Note Request'),
            array('id' => 35, 'name' => 'Retail - Receive Shipments'),
            array('id' => 36, 'name' => 'Mobile App - Return Transfer Note Create'),
            array('id' => 37, 'name' => 'Update Rider Return Transfer Note Request'),
        ));
    }
}
