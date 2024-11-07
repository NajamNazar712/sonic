<?php

use Illuminate\Database\Seeder;

class UpdateActivityTrailForReturnRevertLog extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 484, 'screen_name' => 'Return Revert', 'action'=> 'View'),
            array('id' => 485, 'screen_name' => 'Return Revert', 'action'=> 'Excel Download'),
        ));
        DB::table('module_permissions')->insert(array(
            array('id' => 653, 'name' => 'Return Revert - View', 'module_id' => 9),
        ));
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Report > Return Revert Log', 'url'=>'admin.reports.revert.index', 'permission_id' => 653));

    }
}
