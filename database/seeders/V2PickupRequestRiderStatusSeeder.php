<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class V2PickupRequestRiderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('v2_pickup_request_rider_statuses')->truncate();

        DB::table('v2_pickup_request_rider_statuses')->insert(array(
            array('id' => 1, 'name' => 'Pending'),
            array('id' => 2, 'name' => 'Assigned')
        ));
    }
}
