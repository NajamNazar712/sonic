<?php

use Illuminate\Database\Seeder;

class UpdateWalkInBookingTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('booking_types')->insert(array(
            array('id'=>4,'booking_type'=>'Walk-In')
        ));
    }
}
