<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateActivityTrailForOmniUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 483, 'screen_name' => 'Omni User Setting', 'action'=> 'View'),
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shipper > Omni User Setting', 'url'=>'admin.settings.omni.index', 'permission_id' => 644),
        ));
    }
}
