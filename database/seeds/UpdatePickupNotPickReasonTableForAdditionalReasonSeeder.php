<?php

use Illuminate\Database\Seeder;

class UpdatePickupNotPickReasonTableForAdditionalReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pickup_not_pick_reasons')->insert(array(
            array('id' => 10, 'name' => 'Refused on Call')
        ));
    }
}
