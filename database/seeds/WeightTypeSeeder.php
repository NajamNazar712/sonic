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
            array('id' => 1,'name' => 'Partially Manual'),
            array('id' => 2,'name' => 'Manual'),
            array('id' => 3,'name' => 'Automatic'),
        ));

    }
}
