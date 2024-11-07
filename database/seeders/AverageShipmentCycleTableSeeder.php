<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AverageShipmentCycleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('average_shipment_cycles')->truncate();

        DB::table('average_shipment_cycles')->insert(array(
            array('id' => 1, 'name' => 'Daily'),
            array('id' => 2, 'name' => 'Weekly'),
            array('id' => 3, 'name' => 'Monthly')
        ));
    }
}
