<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTrailForShipmentReversalReport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 803, 'screen_name' => 'Shipment Reversal Report', 'action'=> 'View'),
        ));
    }
}
