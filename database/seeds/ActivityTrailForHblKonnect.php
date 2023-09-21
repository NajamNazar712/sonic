<?php

use Illuminate\Database\Seeder;

class ActivityTrailForHblKonnect extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 894, 'name' => 'Hbl Konnect - Permission', 'module_id' => 9),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 687, 'screen_name' => 'Hbl Konnect ', 'action'=> 'View'),
            array('id' => 688, 'screen_name' => 'Hbl Konnect ', 'action'=> 'Excel'),

        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Hbl Konnect', 'url'=>'admin.reports.hbl_konnect.index', 'permission_id' => 894),           
        ));

    }
}
