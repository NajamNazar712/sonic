<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlistShipperPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        DB::table('module_permissions')->insert(array(
            array('id' => 1005, 'name' => 'Specified Shippers - View', 'module_id' => 14),
            array('id' => 1006, 'name' => 'Specified Shipper (Add Request)', 'module_id' => 14),
            array('id' => 1007, 'name' => 'Specified Shipper (Enable/Disable Request) - Action', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 805, 'screen_name' => 'Specified Shippers', 'action' => 'View'),
        ));


        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Reason Validation > Specified Shipper', 'url' => 'admin.settings.alist_shippers.index', 'permission_id' => 1005),
        ));
    }
}
