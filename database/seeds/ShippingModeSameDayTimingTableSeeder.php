<?php

use Illuminate\Database\Seeder;

class ShippingModeSameDayTimingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shipping_mode_same_day_timings')->insert(array(
            array('timing'=>'6 Hours'),
            array('timing'=>'Same-day')
        ));
    }
}
