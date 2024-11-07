<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateCitiesAddLocationCoordinatesSecond extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cities')->where('id', 288)->update(['location_latitude' => 33.630320, 'location_longitude' => 73.050180]);
    }
}
