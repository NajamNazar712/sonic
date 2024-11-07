<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class V2PickupRequestStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('v2_pickup_request_statuses')->truncate();

        DB::table('v2_pickup_request_statuses')->insert(array(
            array('id' => 1, 'name' => 'Requested'),
            array('id' => 2, 'name' => 'Picked'),
            array('id' => 3, 'name' => 'Not Picked'),
            array('id' => 4, 'name' => 'Cancelled')
        ));
    }
}
