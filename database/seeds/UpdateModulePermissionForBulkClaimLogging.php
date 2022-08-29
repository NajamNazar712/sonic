<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForBulkClaimLogging extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('module_permissions')->insert(array(
            array('id' => 795, 'name' => 'CRM - Bulk Claim Logging', 'module_id' => 18)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 585, 'screen_name' => 'CRM - Bulk Claim Logging', 'action'=> 'View'),
        ));
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'CRM > Bulk Claim Logging', 'url'=>'admin.crm.bulk_claim.index', 'permission_id' => 795),
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 19, 'name' => 'CRM - Bulk Claim Logging')
        ));
    }
}
