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
            array('id' => 1, 'name' => 'Arrival'),
            array('id' => 2, 'name' => 'Forwarding'),
            array('id' => 3, 'name' => 'Cargo Loading'),
            array('id' => 4, 'name' => 'Cargo Unloading'),
            array('id' => 5, 'name' => 'Weight Measurement'),
            array('id' => 6, 'name' => 'Sorting')
        ));
    }
}
