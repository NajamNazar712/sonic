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
        ));
    }
}
