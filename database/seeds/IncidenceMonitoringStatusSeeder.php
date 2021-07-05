<?php

use Illuminate\Database\Seeder;

class IncidenceMonitoringStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('incidence_monitoring_statuses')->truncate();

        DB::table('incidence_monitoring_statuses')->insert(array(
            array('id' => 1, 'status' => 'Open'),
            array('id' => 2, 'status' => 'Under Action'),
            array('id' => 3, 'status' => 'Closed')
        ));
    }
}
