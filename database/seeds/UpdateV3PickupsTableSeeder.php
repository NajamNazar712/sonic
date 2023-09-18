<?php

use Illuminate\Database\Seeder;

class UpdateV3PickupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('v3_pickup_types')->truncate();
        DB::table('v3_pickup_types')->insert(array(
            array('id' => 1, 'name' => 'Regular'),
            array('id' => 2, 'name' => 'WalkIn'),
        ));

        DB::table('v3_pickup_shipment_types')->truncate();
        DB::table('v3_pickup_shipment_types')->insert(array(
            array('id' => 1, 'name' => 'Flyers'),
            array('id' => 2, 'name' => 'Box'),
            array('id' => 3, 'name' => 'Both'),
        ));

        DB::table('v3_pickup_time_ranges')->truncate();
        DB::table('v3_pickup_time_ranges')->insert(array(
            array('id' => 1, 'name' => '9 - 10 AM'),
            array('id' => 2, 'name' => '10 - 11 AM'),
            array('id' => 3, 'name' => '11 - 12 AM'),
            array('id' => 4, 'name' => '12 - 01 PM'),
            array('id' => 5, 'name' => '01 - 02 PM'),
            array('id' => 6, 'name' => '02 - 03 PM'),
            array('id' => 7, 'name' => '03 - 04 PM'),
            array('id' => 8, 'name' => '04 - 05 PM'),
            array('id' => 9, 'name' => '05 - 06 PM'),
            array('id' => 10, 'name' => '06 - 07 PM'),
            array('id' => 11, 'name' => '07 - 08 PM'),
            array('id' => 12, 'name' => '08 - 09 PM'),
            array('id' => 13, 'name' => '09 - 10 PM'),
            array('id' => 14, 'name' => '10 - 11 PM'),
            array('id' => 15, 'name' => '11 - 12 PM'),
        ));



        DB::table('v3_pickup_request_statuses')->truncate();

        DB::table('v3_pickup_request_statuses')->insert(array(
            array('id' => 1, 'name' => 'Pending'),
            array('id' => 2, 'name' => 'Confirmed'),
            array('id' => 3, 'name' => 'Communicated'),
            array('id' => 4, 'name' => 'Accepted'),
            array('id' => 5, 'name' => 'Reached'),
            array('id' => 6, 'name' => 'Picked'),
            array('id' => 7, 'name' => 'Rescheduled'),
        ));

        DB::table('v3_pickup_request_not_pick_reasons')->truncate();
        DB::table('v3_pickup_request_not_pick_reasons')->insert(array(
            array('id' => 1, 'name' => 'Address Closed'),
            array('id' => 2, 'name' => 'Address Incomplete'),
            array('id' => 3, 'name' => 'Incorrect Location'),
            array('id' => 4, 'name' => 'Shipments are not Ready'),
            array('id' => 5, 'name' => 'Contact Person Unavailable'),
            array('id' => 6, 'name' => 'To be Picked Later'),
            array('id' => 7, 'name' => 'Not Attempted'),
            array('id' => 8, 'name' => 'Late Attempted'),
            array('id' => 9, 'name' => 'Accident/Snatching'),
            array('id' => 10, 'name' => 'Refused on Call')
        ));


        DB::table('v3_pickup_services')->truncate();

        DB::table('v3_pickup_services')->insert(array(
            array('id' => 1, 'name' => 'Labour'),
            array('id' => 2, 'name' => 'Lifter'),
            array('id' => 3, 'name' => 'Packaging'),
        ));
    }
}
