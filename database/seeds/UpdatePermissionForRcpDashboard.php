<?php

use Illuminate\Database\Seeder;
use DB;

class UpdatePermissionForProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 760, 'screen_name' => 'Shipment - Reason Validation Required - Dashboard', 'action'=> 'View'),
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp,
             'name' => 'Last Mile > Reason Validation > RVR Dashboard', 
             'url'=>'admin.return.dashboard', 'permission_id' => 943),
        ));
    }
}
