<?php

use Illuminate\Database\Seeder;

class IncidenceMonitoringAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('incidence_monitoring_areas')->truncate();

        DB::table('incidence_monitoring_areas')->insert(array(
            array('id' => 1, 'area' => 'Arrival'),
            array('id' => 2, 'area' => 'Forwarding'),
            array('id' => 3, 'area' => 'Cargo Loading'),
            array('id' => 4, 'area' => 'Cargo Unloading'),
            array('id' => 5, 'area' => 'Weight Measurement'),
            array('id' => 6, 'area' => 'Sorting')
        ));
    }
}
