<?php

use Illuminate\Database\Seeder;

class IncidenceMonitoringNCLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('incidence_monitoring_n_c_levels')->truncate();

        DB::table('incidence_monitoring_n_c_levels')->insert(array(
            array('id' => 1, 'nc_level' => 'Major'),
            array('id' => 2, 'nc_level' => 'Minor')
        ));
    }
}
