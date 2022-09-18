<?php

use Illuminate\Database\Seeder;

class UpdatedPermissionForWholesaleModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activity_trail_actions')->insert(array(
            array('id' => 594, 'screen_name' => 'International Wholesale Accounts', 'action'=> 'View'),
            array('id' => 595, 'screen_name' => 'International Wholesale Accounts', 'action'=> 'Excel Download'),
            array('id' => 596, 'screen_name' => 'International Wholesale Excel Booking', 'action'=> 'View'),
            array('id' => 597, 'screen_name' => 'International Wholesale Excel Booking', 'action'=> 'Excel Download'),
            array('id' => 598, 'screen_name' => 'International Wholesale Invoice', 'action'=> 'View'),
            array('id' => 599, 'screen_name' => 'International Wholesale Invoice', 'action'=> 'Excel Download'),
        ));

        DB::table('module_permissions')->insert(array(
            array('id' => 799, 'name' => 'International Wholesale Accounts - View', 'module_id' => 33),
            array('id' => 800, 'name' => 'International Wholesale Accounts - Add Shipper', 'module_id' => 33),
            array('id' => 801, 'name' => 'International Wholesale Accounts - Edit Shipper', 'module_id' => 33),
            array('id' => 802, 'name' => 'International Wholesale Accounts - Enable/Disable Shipper', 'module_id' => 33),
            array('id' => 803, 'name' => 'International Wholesale Accounts - Add Margin', 'module_id' => 33),
            array('id' => 804, 'name' => 'International Wholesale Excel Booking - View', 'module_id' => 33),
            array('id' => 805, 'name' => 'International Wholesale Excel Booking - Edit Shipment', 'module_id' => 33),
            array('id' => 806, 'name' => 'International Wholesale Excel Booking - Cancel Shipment', 'module_id' => 33),
            array('id' => 807, 'name' => 'International Wholesale Invoices - View', 'module_id' => 8),
            array('id' => 808, 'name' => 'International Wholesale Invoices - Edit', 'module_id' => 8),
            array('id' => 809, 'name' => 'International Wholesale Invoices - View History', 'module_id' => 8),
            array('id' => 810, 'name' => 'International Wholesale Invoices - Reimbursement Print', 'module_id' => 8),
            array('id' => 811, 'name' => 'International Wholesale Invoices - Consolidated Service Invoice Print', 'module_id' => 8),
            array('id' => 813, 'name' => 'International Wholesale Invoices - Mark Received', 'module_id' => 8),
        ));

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > International > Wholesale Accounts', 'url'=>'admin.international.wholesale.accounts.index', 'permission_id' => 799),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > International > Wholesale Excel Booking', 'url'=>'admin.international.wholesale.excel.index', 'permission_id' => 804),
        array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Financials > Invoices > Wholesale Invoices', 'url'=>'admin.international.wholesale.invoices.index', 'permission_id' => 807)
        );
    }
}
