<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailActionForAirwayBillRights extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 519, 'screen_name' => 'CN Print Rights', 'action'=> 'View'),
        ));
    }
}
