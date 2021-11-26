<?php

use Illuminate\Database\Seeder;

class ReturnRevertModulePermissionActivityTrailScreenList extends Seeder
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
            array('id' => 475, 'screen_name' => 'Return Revert', 'action'=> 'View'),
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Return > Revert', 'url'=>'admin.return.revert.index', 'permission_id' => 643),
       ));

        DB::table('module_permissions')->insert(array(
            array('id' => 643, 'name' => 'Return Revert - View', 'module_id' => 7),
        ));
    }
}
