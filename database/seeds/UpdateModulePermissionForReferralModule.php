<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForReferralModule extends Seeder
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
            array('id' => 701, 'name' => 'Referral Module', 'module_id' => 14)
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 520, 'screen_name' => 'Referral Module', 'action'=> 'View'),
        ));

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shippers > Referral Module', 'url'=>'admin.settings.referral.index', 'permission_id' => 701),
        ));
    }
}
