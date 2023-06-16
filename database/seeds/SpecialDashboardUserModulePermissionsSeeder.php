<?php

use Illuminate\Database\Seeder;
use App\Http\Models\Admin\ModulePermission;
use App\Http\Models\Admin\ActivityTrailAction;

class SpecialDashboardUserModulePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ModulePermission::whereIn('id',[873, 874, 875])->delete();

        DB::table('module_permissions')->insert(array(
            array('id' => 873, 'name' => 'Special Dashboard Accounts - View', 'module_id' => 2),
            array('id' => 874, 'name' => 'Special Dashboard Accounts - Add', 'module_id' => 2),
            array('id' => 875, 'name' => 'Special Dashboard Accounts - Edit', 'module_id' => 2),
        ));

        ActivityTrailAction::whereIn('id',[662, 663, 664])->delete();

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 662, 'screen_name' => 'Special Dashboard Accounts', 'action'=> 'View'),
            array('id' => 663, 'screen_name' => 'Special Dashboard Accounts Excel', 'action'=> 'Download'),
            array('id' => 664, 'screen_name' => 'Special Dashboard Accounts - Add', 'action'=> 'View'),
        ));
    }
}
