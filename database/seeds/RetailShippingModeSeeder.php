<?php

use Illuminate\Database\Seeder;

class RetailShippingModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('retail_shipping_modes')->truncate();

        DB::table('retail_shipping_modes')->insert(array(
            array('id' => 1, 'name' => 'Overland'),
            array('id' => 2, 'name' => 'Overnight'),
            array('id' => 3, 'name' => 'COD'),
            array('id' => 4, 'name' => 'Detained'),
            array('id' => 5, 'name' => 'Trax Box'),
            array('id' => 6, 'name' => 'Flyers'),
            array('id' => 7, 'name' => 'Hard Docs')
        ));
    }
}
