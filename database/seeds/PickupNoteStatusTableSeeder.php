<?php

use Illuminate\Database\Seeder;

class PickupNoteStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pickup_note_statuses')->insert(array(
            array('id' => 1, 'name' => 'Assigned'),
            array('id' => 2, 'name' => 'Confirmed'),
            array('id' => 3, 'name' => 'Completed'),
            array('id' => 4, 'name' => 'Incomplete'),
            array('id' => 5, 'name' => 'Cancelled'),
        ));
    }
}
