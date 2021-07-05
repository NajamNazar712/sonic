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
            array('id' => 1, 'case_nature' => 'Improper Handling'),
            array('id' => 2, 'case_nature' => 'Improper Loading'),
            array('id' => 3, 'case_nature' => 'Improper Unloading'),
            array('id' => 4, 'case_nature' => 'Throwing'),
            array('id' => 5, 'case_nature' => 'Walking Over Shipments'),
            array('id' => 6, 'case_nature' => 'Weight Not Measured Properly'),
            array('id' => 7, 'case_nature' => 'Opening The Parcel'),
            array('id' => 8, 'case_nature' => 'Handling Equipment Not Used'),
            array('id' => 9, 'case_nature' => 'Vehicles Door Not Closed'),
            array('id' => 10, 'case_nature' => 'Heat or Fire Usage Near Shipments'),
            array('id' => 11, 'case_nature' => 'Overloading')
        ));
    }
}
