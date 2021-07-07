<?php

use Illuminate\Database\Seeder;

class IncidenceMonitoringCaseNatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('incidence_monitoring_case_natures')->truncate();

        DB::table('incidence_monitoring_case_natures')->insert(array(
            array('id' => 1, 'name' => 'Improper Handling'),
            array('id' => 2, 'name' => 'Improper Loading'),
            array('id' => 3, 'name' => 'Improper Unloading'),
            array('id' => 4, 'name' => 'Throwing'),
            array('id' => 5, 'name' => 'Walking Over Shipments'),
            array('id' => 6, 'name' => 'Weight Not Measured Properly'),
            array('id' => 7, 'name' => 'Opening The Parcel'),
            array('id' => 8, 'name' => 'Handling Equipment Not Used'),
            array('id' => 9, 'name' => 'Vehicles Door Not Closed'),
            array('id' => 10, 'name' => 'Heat or Fire Usage Near Shipments'),
            array('id' => 11, 'name' => 'Overloading')
        ));
    }
}
