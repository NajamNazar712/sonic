<?php

use Illuminate\Database\Seeder;

class FTLRequestStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ftl_request_statuses')->insert(array(
            array('id' => 1, 'status' => 'Requested'),
            array('id' => 2, 'status' => 'Estimated'),
            array('id' => 3, 'status' => 'Approved'),
            array('id' => 4, 'status' => 'Rejected'),
            array('id' => 5, 'status' => 'Booked'),
        ));
    }
}
