<?php

use Illuminate\Database\Seeder;

class PackagingRequestStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('packaging_request_statuses')->truncate();

        DB::table('packaging_request_statuses')->insert(array(
            array('id' => 1, 'name' => 'Booked'),
            array('id' => 2, 'name' => 'Confirmed'),
            array('id' => 3, 'name' => 'Dispatched'),
            array('id' => 4, 'name' => 'Completed'),
            array('id' => 5, 'name' => 'Replenished'),
            array('id' => 6, 'name' => 'Cancelled')
        ));
    }
}
