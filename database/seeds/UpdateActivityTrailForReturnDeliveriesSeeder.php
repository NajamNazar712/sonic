<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailForReturnDeliveriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 416, 'screen_name' => 'Return Deliveries', 'action'=> 'View'),
            array('id' => 417, 'screen_name' => 'Return Deliveries', 'action'=> 'Excel Download')
        ));
    }
}
