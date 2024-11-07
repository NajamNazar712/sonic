<?php

use Illuminate\Database\Seeder;

class SeederAdminOTPActivityTrail extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 544, 'screen_name' => 'Admin OTP', 'action'=> 'View'),
            array('id' => 545, 'screen_name' => 'Admin OTP', 'action'=> 'Excel Download'),
            array('id' => 546, 'screen_name' => 'Admin OTP', 'action'=> 'OTP Updated'),
            array('id' => 547, 'screen_name' => 'Rider OTP (Login & Delivery Note)', 'action'=> 'OTP Updated'),
        ));
    }
}
