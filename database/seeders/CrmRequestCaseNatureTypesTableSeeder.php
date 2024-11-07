<?php

use Illuminate\Database\Seeder;

class CrmRequestCaseNatureTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('crm_request_case_nature_types')->truncate();

        DB::table('crm_request_case_nature_types')->insert(array(
            array('id' => 1, 'nature_id' => 1, 'type' => 'Payments'),
            array('id' => 2, 'nature_id' => 1, 'type' => 'Delay in Delivery'),
            array('id' => 3, 'nature_id' => 1, 'type' => 'Delay in Pickup'),
            array('id' => 4, 'nature_id' => 1, 'type' => 'Incorrect COD'),
            array('id' => 5, 'nature_id' => 1, 'type' => 'Return'),
            array('id' => 6, 'nature_id' => 1, 'type' => 'Courier Misbehavior'),
            array('id' => 7, 'nature_id' => 1, 'type' => 'Wrong COD'),
            array('id' => 8, 'nature_id' => 1, 'type' => 'Booking Portal Issue'),
            array('id' => 9, 'nature_id' => 1, 'type' => 'Internal'),
            array('id' => 10, 'nature_id' => 1, 'type' => 'Fake Reason'),
            array('id' => 11, 'nature_id' => 2, 'type' => 'Address Change'),
            array('id' => 12, 'nature_id' => 2, 'type' => 'COD Change'),
            array('id' => 13, 'nature_id' => 2, 'type' => 'Alternate Contact Number'),
            array('id' => 14, 'nature_id' => 2, 'type' => 'Urgent Delivery')
        ));
    }
}
