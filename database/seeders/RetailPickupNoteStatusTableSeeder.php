<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RetailPickupNoteStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('retail_pickup_note_statuses')->truncate();

        DB::table('retail_pickup_note_statuses')->insert(array(
            array('id' => 1, 'name' => 'Pending'),
            array('id' => 2, 'name' => 'Rider Assigned'),
            array('id' => 3, 'name' => 'Shipments Arrived'),
            array('id' => 4, 'name' => 'Cash Collected')
        ));
    }
}
