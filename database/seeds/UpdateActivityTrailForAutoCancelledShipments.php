<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailForAutoCancelledShipments extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 488, 'screen_name' => 'Auto Cancellation Shipments', 'action'=> 'View'),
        ));
    }
}
