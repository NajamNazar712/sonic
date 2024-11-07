<?php

use Illuminate\Database\Seeder;

class OneLinkShipmentWiseSummaryAndReportPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('module_permissions')->insert(array(
            array('id' => 824, 'name' => '1link shipment wise summary - View', 'module_id' => 9),
            array('id' => 825, 'name' => '1link shipment Payment Charges Setting', 'module_id' => 14),
        ));

        DB::table('activity_trail_actions')->insert(array(
            array('id' => 619, 'screen_name' => '1link shipment wise summary', 'action' => 'View'),
            array('id' => 620, 'screen_name' => '1link shipment wise summary Excel', 'action' => 'Excel Download'),
            array('id' => 621, 'screen_name' => '1link shipment Payment Charges Setting Screen', 'action' => 'View'),
            array('id' => 622, 'screen_name' => '1link shipment Payment Charges Setting Screen', 'action' => 'Charges Update'),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > 1link shipment wise summary', 'url' => 'admin.reports.one_link_charges_summary.index', 'permission_id' => 824),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Financials > 1Link Payment Charges', 'url' => 'admin.settings.onelink_payment_charges.index', 'permission_id' => 825)
        ));
    }
}
