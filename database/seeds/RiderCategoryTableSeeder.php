<?php

use Illuminate\Database\Seeder;

class RiderCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rider_categories')->insert(array(
            array('name'=>'Light'),
            array('name'=>'Heavy'),
            array('name'=>'Pickup'),
            array('name'=>'Express'),
            array('name'=>'Other'),

        ));
    }
}
