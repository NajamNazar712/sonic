<?php

use Illuminate\Database\Seeder;

class ZonesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('zones')->truncate();
        DB::table('zones')->insert(array(
            array('id' => 1, 'name' => 'South Zone', 'gst' => 0.13),
            array('id' => 2, 'name' => 'Central Zone', 'gst' => 0.16),
            array('id' => 3, 'name' => 'North Zone', 'gst' => 0.16)
        ));
    }
}
