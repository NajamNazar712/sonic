<?php

use Illuminate\Database\Seeder;

class ActivityTrailAndPermissionSeederForInternationalRates extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 555, 'screen_name' => 'International Extra Service Charges', 'action'=> 'View'),
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 750, 'name' => 'International Extra Service Charges - View', 'module_id' => 33)
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > International Extra Service Charges', 'url'=>'admin.international.extra_service_charges.index', 'permission_id' => 750));

    }
}
