<?php

use Illuminate\Database\Seeder;

class MakeSeederForRiderMainCategory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rider_main_categories')->truncate();
        DB::table('rider_main_categories')->insert(array(
            array('name'=>'Pickup Rider'),
            array('name'=>'Delivery Rider'),
        ));
    }
}
