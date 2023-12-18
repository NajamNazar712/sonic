<?php

use Illuminate\Database\Seeder;

class WeightTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('weight_types')->truncate();

        DB::table('weight_types')->insert(array(
            array('name' => 'Partially Manual'),
            array('name' => 'Manual'),
            array('name' => 'Automatic'),
        ));

    }
}
