<?php

use Illuminate\Database\Seeder;

class ActivityTrailAndPermissionForInternationalEconomyStandardRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 800, 'screen_name' => 'Retail International Economy Rates Excel Upload', 'action'=> 'View'),
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 992, 'name' => 'Retail International Economy Rates Excel Upload - View', 'module_id' => 14)
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Retail > International Economy Rates Excel Upload', 'url'=>'admin.retail.international.economy_rates.index', 'permission_id' => 992));

    }
}
