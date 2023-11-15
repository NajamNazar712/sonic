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
            array('id' => 1, 'name' => '08 - 09 AM', 'city_id' => 202),
            array('id' => 2, 'name' => '09 - 10 AM', 'city_id' => 202),
            array('id' => 3, 'name' => '10 - 11 AM', 'city_id' => 202),
            array('id' => 4, 'name' => '11 - 12 AM', 'city_id' => 202),
            array('id' => 5, 'name' => '12 - 01 PM', 'city_id' => 202),
            array('id' => 6, 'name' => '01 - 02 PM', 'city_id' => 202),
            array('id' => 7, 'name' => '02 - 03 PM', 'city_id' => 202),
            array('id' => 8, 'name' => '03 - 04 PM', 'city_id' => 202),
            array('id' => 9, 'name' => '04 - 05 PM', 'city_id' => 202),
            array('id' => 10, 'name' => '08 - 09 AM', 'city_id' => 223),
            array('id' => 11, 'name' => '09 - 10 AM', 'city_id' => 223),
            array('id' => 12, 'name' => '10 - 11 AM', 'city_id' => 223),
            array('id' => 13, 'name' => '11 - 12 AM', 'city_id' => 223),
            array('id' => 14, 'name' => '12 - 01 PM', 'city_id' => 223),
            array('id' => 15, 'name' => '01 - 02 PM', 'city_id' => 223),
            array('id' => 16, 'name' => '02 - 03 PM', 'city_id' => 223),
            array('id' => 17, 'name' => '03 - 04 PM', 'city_id' => 223),
        ));



        DB::table('v3_pickup_request_statuses')->truncate();

        DB::table('v3_pickup_request_statuses')->insert(array(
            array('id' => 1, 'name' => 'Pending'),
            array('id' => 2, 'name' => 'Confirmed'),
            array('id' => 3, 'name' => 'Communicated'),
            array('id' => 4, 'name' => 'Accepted'),
            array('id' => 5, 'name' => 'Reached'),
            array('id' => 6, 'name' => 'Picked'),
            array('id' => 7, 'name' => 'Not Picked'),
            array('id' => 8, 'name' => 'Rescheduled'),
        ));

        DB::table('v3_pickup_request_reasons')->truncate();
        DB::table('v3_pickup_request_reasons')->insert(array(
            array('id' => 1, 'name' => 'Address Closed', 'type' => 2),
            array('id' => 2, 'name' => 'Address Incomplete', 'type' => 2),
            array('id' => 3, 'name' => 'Incorrect Location', 'type' => 2),
            array('id' => 4, 'name' => 'Shipments are not Ready', 'type' => 2),
            array('id' => 5, 'name' => 'Contact Person Unavailable', 'type' => 2),
            array('id' => 6, 'name' => 'To be Picked Later', 'type' => 2),
            array('id' => 7, 'name' => 'Not Attempted', 'type' => 2),
            array('id' => 8, 'name' => 'Late Attempted', 'type' => 2),
            array('id' => 9, 'name' => 'Accident/Snatching', 'type' => 2),
            array('id' => 10, 'name' => 'Refused on Call', 'type' => 2)
        ));


        DB::table('v3_pickup_services')->truncate();

        DB::table('v3_pickup_services')->insert(array(
            array('id' => 1, 'name' => 'Labour'),
            array('id' => 2, 'name' => 'Lifter'),
            array('id' => 3, 'name' => 'Packaging'),
        ));
    }
}
