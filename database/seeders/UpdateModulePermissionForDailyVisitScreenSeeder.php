<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForDailyVisitScreenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 791, 'name' => 'Daily Visits - View', 'module_id' => 2),
            array('id' => 792, 'name' => 'Daily Visits - Edit', 'module_id' => 2),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 579, 'screen_name' => 'Daily Visit Screen', 'action'=> 'View'),
            array('id' => 580, 'screen_name' => 'Daily Visit Screen', 'action'=> 'Excel Download'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shippers > Daily Visit', 'url'=>'admin.daily_visit.screen.index', 'permission_id' => 791));
    }
}
