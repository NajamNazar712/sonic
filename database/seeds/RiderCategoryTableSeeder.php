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
            array('name'=>'Light Rider'),
            array('name'=>'Heavy Rider'),
            array('name'=>'Pickup Rider'),
            array('name'=>'Express Rider'),
            array('name'=>'Other'),

        ));
    }
}
