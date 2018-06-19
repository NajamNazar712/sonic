<?php

use Illuminate\Database\Seeder;
use App\Http\Models\BookingType;
class BookingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('booking_types')->truncate();
        DB::table('booking_types')->insert(array(
            array('booking_type'=>'Regular'),
            array('booking_type'=>'Replacement'),
            array('booking_type'=>'Try & Buy'),

        ));
    }
}
