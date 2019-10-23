<?php

use Illuminate\Database\Seeder;

class PickupNotPickReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pickup_not_pick_reasons')->insert(array(
            array('id' => 1, 'name' => 'Address Closed'),
            array('id' => 2, 'name' => 'Address Incomplete'),
            array('id' => 3, 'name' => 'Incorrect Location'),
            array('id' => 4, 'name' => 'Shipments are not Ready'),
            array('id' => 5, 'name' => 'Contact Person Unavailable'),
            array('id' => 6, 'name' => 'To be Picked Later'),
            array('id' => 7, 'name' => 'Not Attempted'),
            array('id' => 8, 'name' => 'Late Attempted'),
            array('id' => 9, 'name' => 'Accident/Snatching')
        ));
    }
}
