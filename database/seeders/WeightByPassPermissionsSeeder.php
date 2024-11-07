<?php

use Illuminate\Database\Seeder;

class WeightByPassPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 761, 'name' => 'Weight ByPass - View', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 556, 'screen_name' => 'Weight ByPass', 'action'=> 'View'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > First Mile > Weight ByPass', 'url'=>'admin.settings.pickup.weight_bypass', 'permission_id' => 761),
        ));
    }
}
