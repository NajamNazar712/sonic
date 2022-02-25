<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForReturnReasonMandatorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 684, 'name' => 'Return Reason Mandatory - View', 'module_id' => 14),
            array('id' => 685, 'name' => 'Return Reason Mandatory - Add', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 510, 'screen_name' => 'Return Reason Mandatory', 'action'=> 'View'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Last Mile > Return Reason Mandatory', 'url'=>'admin.settings.return_reason_mandatory.index', 'permission_id' => 684),
        ));
    }
}
