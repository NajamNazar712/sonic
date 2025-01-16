<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AddNegativePayablePermissionSeeder extends Seeder
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
            array('id' => 982, 'name' => 'Shipper Negative Payable Settings - View', 'module_id' => 14),
            array('id' => 983, 'name' => 'Shipper Negative Payable Settings - Update', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 787, 'screen_name' => 'Shipper Negative Payable Settings', 'action' => 'View'),
            array('id' => 788, 'screen_name' => 'Shipper Negative Payable Settings ', 'action' => 'Update'),
        ));


        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Financials > Shipper Negative Payable Settings', 'url' => 'admin.settings.shipper_negative_payable.index', 'permission_id' => 982),
        ));
    }
}
