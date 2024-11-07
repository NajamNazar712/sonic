<?php

use Illuminate\Database\Seeder;

class UpdateAdminScreenListSprint66 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert(array(
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shippers > Accounts > Active Today', 'url'=>'admin.accounts.active.today', 'permission_id' => 470),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Activity Trail', 'url'=>'admin.activity_trail.index', 'permission_id' => 471),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Weight QC', 'url'=>'admin.reports.weight_qc.index', 'permission_id' => 444),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > In Transit Report Bag Wise', 'url'=>'admin.reports.master_cargo.bag.in_transit.index', 'permission_id' => 472),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Master Cargo Short Receive Shipments', 'url'=>'admin.reports.master_cargo.short_received_shipments.index', 'permission_id' => 476),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Reports > Petty Cash Expense Summary Report', 'url'=>'admin.reports.petty_cash_expense_summary.index', 'permission_id' => 395),
            array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > Retail > Store Management > Retail Users', 'url'=>'admin.retail.users.index', 'permission_id' => 474),
        ));
    }
}
