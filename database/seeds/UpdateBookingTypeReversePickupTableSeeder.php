<?php

use Illuminate\Database\Seeder;

class UpdateBookingTypeReversePickupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('booking_types')->insert(array(
            array('id' => 5, 'booking_type' => 'Reverse Pickup')
        ));
    }
}
