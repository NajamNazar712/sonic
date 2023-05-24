<?php

use Illuminate\Database\Seeder;

class ActivityTrailForRiderRemarks extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 656, 'screen_name' => 'Rider Remarks', 'action'=> 'View'),
            array('id' => 657, 'screen_name' => 'Rider Remarks', 'action'=> 'Excel Download'),
          
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Rider Remarks', 'url'=>'admin.management.riders.rider_remarks.index', 'permission_id' => 862),           
        ));
    }
}
