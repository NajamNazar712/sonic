<?php

use Illuminate\Database\Seeder;

class UpdateModulePermissionShipperOriginChangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_permissions')->insert(array(
            array('id' => 667, 'name' => 'Shipper Origin Change', 'module_id' => 14),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Shipper > Shipper Origin Change', 'url'=>'admin.settings.shippers_origin_change.index', 'permission_id' => 667),
        ));
    }
}
