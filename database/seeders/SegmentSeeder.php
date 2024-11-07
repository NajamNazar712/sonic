<?php

use Illuminate\Database\Seeder;

class SegmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('segments')->insert(array(
            array('id' => 1, 'name' => 'General Logistics'),
            array('id' => 2, 'name' => 'E-comm')
        ));
    }
}
