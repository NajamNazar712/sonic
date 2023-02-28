<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionsForDeliveryAutoVerificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now();

        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Last Mile > Auto Delivery Note Verification', 'url'=>'admin.settings.auto_delivery_note_verification.index', 'permission_id' => 836)
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 836, 'name' => 'Auto Delivery Note Verification - View', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 636, 'screen_name' => 'Auto Delivery Note Verification', 'action'=> 'View'),
        ));
    }
}
