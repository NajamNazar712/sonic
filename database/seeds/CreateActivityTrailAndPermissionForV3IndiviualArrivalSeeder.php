<?php

use Illuminate\Database\Seeder;

class CreateActivityTrailAndPermissionForV3IndiviualArrivalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 924, 'name' => 'V3 Indiviual Arrival', 'module_id' => 3),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 730, 'screen_name' => 'V3 Indiviual Arrival', 'action' => 'View'),
            array('id' => 731, 'screen_name' => 'V3 Indiviual Arrival', 'action' => 'Excel'),

        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'First Mile > Pickup > V3 Indiviual Arrival', 'url' => 'admin.v3_pickups.arrival.individual.index', 'permission_id' => 924),
        ));
    }
}
