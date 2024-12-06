<?php

use Illuminate\Database\Seeder;

class UpdateEntryMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('shipment_scanning_journeys')
            ->where('entry_method', 0)
            ->where('screen_location_id', 30)
            ->update(['entry_method' => 1]);
    }
}
