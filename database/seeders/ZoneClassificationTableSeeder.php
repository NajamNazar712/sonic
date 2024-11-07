<?php

use Illuminate\Database\Seeder;

class ZoneClassificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('zone_classification')->truncate();
        DB::table('zone_classification')->insert(array(
            array('id' => 1, 'name' => 'Overnight/Same-day'),
            array('id' => 2,  'name' => 'Overland/Detain')
        ));
    }
}
