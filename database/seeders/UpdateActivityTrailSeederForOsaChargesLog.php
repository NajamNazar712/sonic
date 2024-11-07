<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailSeederForOsaChargesLog extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 481, 'screen_name' => 'OSA Charges Log - Report', 'action'=> 'View'),
            array('id' => 482, 'screen_name' => 'OSA Charges Log - Report', 'action'=> 'Excel Download')
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > OSA Charges Log', 'url'=>'admin.reports.osa_charges.index', 'permission_id' => 647)
        ));
    }
}
