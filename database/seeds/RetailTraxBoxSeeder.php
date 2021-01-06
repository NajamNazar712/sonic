<?php

use Illuminate\Database\Seeder;

class RetailTraxBoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('retail_trax_boxes')->truncate();

        DB::table('retail_trax_boxes')->insert(array(
            array('id' => 1, 'name' => '2kg'),
            array('id' => 2, 'name' => '5kg'),
            array('id' => 3, 'name' => '10kg'),
            array('id' => 4, 'name' => '15kg'),
            array('id' => 5, 'name' => '20kg'),
            array('id' => 6, 'name' => '30kg')
        ));
    }
}
