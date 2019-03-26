<?php

use Illuminate\Database\Seeder;

class StandardFuelSurchargesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('standard_fuel_surcharges')->truncate();
        DB::table('standard_fuel_surcharges')->insert(array(
            array('shipping_mode_id'=>1,'fuel_surcharge'=>3),
            array('shipping_mode_id'=>2,'fuel_surcharge'=>3),
            array('shipping_mode_id'=>3,'fuel_surcharge'=>3),
            array('shipping_mode_id'=>4,'fuel_surcharge'=>3),
        ));
    }
}
