<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForNewMonthClosingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->whereIn('id', [408,409,410,411,412,413,414,415,417,418])->delete();
        DB::table('admins_screen_list')->whereIn('permission_id', [408,409,414,417,418])->delete();
        DB::table('admins_screen_list')->where('url', 'admin.month_closing.index')->delete();

        DB::table('module_permissions')->insert(array(
            array('id' => 408, 'name' => 'Month Closing Pending - View', 'module_id' => 15),
            array('id' => 409, 'name' => 'Month Closing Resolved - View', 'module_id' => 15),
            array('id' => 410, 'name' => 'Month Closing Resolved - Action', 'module_id' => 15),
            array('id' => 411, 'name' => 'Month Closing Closing Type - Action', 'module_id' => 15),
            array('id' => 412, 'name' => 'Month Closing Assign Responsible - Action', 'module_id' => 15),
            array('id' => 413, 'name' => 'Month Closing Close - Action', 'module_id' => 15),
            array('id' => 414, 'name' => 'Month Closing Report - Individual', 'module_id' => 9),
            array('id' => 415, 'name' => 'Month Closing Report - Pivot', 'module_id' => 9),
            array('id' => 417, 'name' => 'Month Closing Setting - Closing Type', 'module_id' => 14),
            array('id' => 418, 'name' => 'Month Closing Setting - Closing Status', 'module_id' => 14),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support  >  Month Closing  >  Pending', 'url'=>'admin.month_closing.pending.index', 'permission_id' => 408),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support  >  Month Closing  >  Resolved', 'url'=>'admin.month_closing.resolved.index', 'permission_id' => 409),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports  >  Month Closing-Individual', 'url'=>'admin.reports.month_closing.individual.index', 'permission_id' => 414),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Setting  > Support >  Month Closing  >  Types', 'url'=>'admin.settings.month_closing.types.index', 'permission_id' => 417),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Setting  > Support >  Month Closing  >  Status', 'url'=>'admin.settings.month_closing.status.index', 'permission_id' => 418),

        ));

    }
}
