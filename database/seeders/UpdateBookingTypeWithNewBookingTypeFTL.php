<?php

use Illuminate\Database\Seeder;

class UpdateBookingTypeWithNewBookingTypeFTL extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('booking_types')->insert(array(
            array('id' => 6, 'booking_type' => 'FTL')
        ));
    }
}
