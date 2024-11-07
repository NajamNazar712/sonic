<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailActionForRiderDeliveryNoteOtp extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 414, 'screen_name' => 'Rider Delivery Note OTP', 'action'=> 'View'),
            array('id' => 415, 'screen_name' => 'Rider Delivery Note OTP', 'action'=> 'Excel Download')
        ));
    }
}
