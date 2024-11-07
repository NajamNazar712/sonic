<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailForDwsReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 476, 'screen_name' => 'DWS Report', 'action'=> 'View'),
            array('id' => 477, 'screen_name' => 'DWS Report', 'action'=> 'Excel Download'),
        ));
    }
}
