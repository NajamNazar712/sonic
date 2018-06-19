<?php

use Illuminate\Database\Seeder;

class StandardBookingTypeChargesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('standard_booking_type_charges')->truncate();
        DB::table('standard_booking_type_charges')->insert(array(
            array('shipping_mode_id'=>1,'replacement_charges'=>200,'try_and_buy_charges'=>250),
            array('shipping_mode_id'=>2,'replacement_charges'=>450,'try_and_buy_charges'=>450),
            array('shipping_mode_id'=>3,'replacement_charges'=>500,'try_and_buy_charges'=>500),
            array('shipping_mode_id'=>4,'replacement_charges'=>250,'try_and_buy_charges'=>300),
        ));
    }
}
