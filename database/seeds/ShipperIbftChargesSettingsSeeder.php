<?php

use Illuminate\Database\Seeder;

class ShipperIbftChargesSettingsSeeder extends Seeder
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
            array('id' => 913, 'name' => 'Shipper IBFT Charges Settings - View', 'module_id' => 14),
            array('id' => 914, 'name' => 'Shipper IBFT Charges Settings - Update', 'module_id' => 14),
        ));


        DB::table('activity_trail_actions')->insert(array(
            array('id' => 712, 'screen_name' => 'Shipper IBFT Charges Settings', 'action' => 'View'),
            array('id' => 713, 'screen_name' => 'Shipper IBFT Charges Settings ', 'action' => 'Excel Download'),
            array('id' => 714, 'screen_name' => 'Shipper IBFT Charges Settings ', 'action' => 'Update'),
        ));


        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Financials > Shipper IBFT Charges Settings', 'url' => 'admin.settings.shipper_ibft_charges_settings.index', 'permission_id' => 913),
        ));
    }
}
