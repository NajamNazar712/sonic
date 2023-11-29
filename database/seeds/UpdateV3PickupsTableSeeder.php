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
            array('id' => 7, 'name' => 'Rejected'),
            array('id' => 8, 'name' => 'Rescheduled'),
            array('id' => 9, 'name' => 'Not Picked'),
        ));

        DB::table('v3_pickup_request_reasons')->truncate();
        DB::table('v3_pickup_request_reasons')->insert(array(
            array('id' => 1, 'name' => 'Address Closed', 'type' => 0,'active'=>'t'),
            array('id' => 2, 'name' => 'Address Incomplete', 'type' => 0,'status'=>'Y','active'=>'t'),
            array('id' => 3, 'name' => 'Incorrect Location', 'type' => 0,'active'=>'t'),
            array('id' => 4, 'name' => 'Shipments are not Ready', 'type' => 0,'active'=>'t'),
            array('id' => 5, 'name' => 'Contact Person Unavailable', 'type' => 0,'active'=>'t'),
            array('id' => 6, 'name' => 'To be Picked Later', 'type' => 0,'status'=>'Y','active'=>'t'),
            array('id' => 7, 'name' => 'Not Attempted', 'type' => 0,'active'=>'t'),
            array('id' => 8, 'name' => 'Late Attempted', 'type' => 0,'active'=>'t'),
            array('id' => 9, 'name' => 'Accident/Snatching', 'type' => 0,'status'=>'Y','active'=>'t'),
            array('id' => 10, 'name' => 'Refused on Call', 'type' =>0,'active'=>'t'),
            array('id' => 10, 'name' => 'Pickup', 'type' =>1,'active'=>'t')

        ));


        DB::table('v3_pickup_services')->truncate();

        DB::table('v3_pickup_services')->insert(array(
            array('id' => 1, 'name' => 'Labour'),
            array('id' => 2, 'name' => 'Lifter'),
            array('id' => 3, 'name' => 'Packaging'),
        ));
    }
}
