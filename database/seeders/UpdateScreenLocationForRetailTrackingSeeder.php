<?php

use Illuminate\Database\Seeder;

class UpdateScreenLocationForRetailTrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipment_scanning_screen_locations')->insert(array(
            array('id' => 18, 'name' => 'Retail - Tracking'),
            array('id' => 19, 'name' => 'Retail - Cancel Add'),
        ));
    }
}
