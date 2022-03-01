<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionForConsigneeSMSExpireTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 683, 'name' => 'Consignee SMS Expiration Time', 'module_id' => 14),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Last Mile > Consignee SMS Expiration Time', 'url'=>'admin.settings.consignee_sms_expire.index', 'permission_id' => 683));
    }
}
