<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class WallerShipperSettingPermissionSeeder extends Seeder
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
            array('id' => 1023, 'name' => 'Wallet Shippers - View', 'module_id' => 14),
            array('id' => 1024, 'name' => 'Wallet Shipper (Add Request)', 'module_id' => 14),
            array('id' => 1025, 'name' => 'Wallet Shipper (Enable/Disable Request) - Action', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 818, 'screen_name' => 'Wallet Shippers', 'action'=> 'View'),
        ));


        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Setting > Shippers > Wallet Shipper', 'url'=>'admin.settings.wallet_shippers.index', 'permission_id' => 1023),
        ));
    }
}
