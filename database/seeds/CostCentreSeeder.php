<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CostCentreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cost_centres')->truncate();

        DB::table('cost_centres')->insert(array(
            array('id' => 1, 'name' => 'ECOM'),
            array('id' => 2, 'name' => 'MMS'),
            array('id' => 3, 'name' => 'Retail'),
            array('id' => 4, 'name' => 'Warehouse'),
            array('id' => 5, 'name' => 'Logistics'),
            array('id' => 6, 'name' => 'International'),
            array('id' => 7, 'name' => 'Temperature Control'),
        ));
    }
}
