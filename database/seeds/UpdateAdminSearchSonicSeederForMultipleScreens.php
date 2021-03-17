<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateAdminSearchSonicSeederForMultipleScreens extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('admins_screen_list')->where('name','Settings  >  Last Mile  >  Route Management')->update(['name' => 'Settings > Last Mile > Return Confirmation Pending Shipment Selection Time']);
        DB::table('admins_screen_list')->where('name','Shippers  >  Packaging  >  Requests > Packaging Material Stock Requests')->delete();
        DB::table('admins_screen_list')->where('name','First Mile  >  Pickups  >  Pending Pickups')->delete();
        DB::table('admins_screen_list')->where('name','First Mile  >  Pickups  >  Assigned Pickups')->delete();
        DB::table('admins_screen_list')->where('name','First Mile  >  Pickups  >  Receive Pickups')->delete();
        DB::table('admins_screen_list')->where('name','First Mile  >  Pickups  >  Receive Pickups')->delete();
        DB::table('admins_screen_list')->where('name','First Mile  >  Pickups  >  Pickups History')->delete();
        DB::table('admins_screen_list')->where('name','First Mile  >  Pickups  >  Rider Pickups')->delete();
        DB::table('admins_screen_list')->where('name','Supply Chain  >  Cargo  >  Pending Shipments for Cargo')->delete();
        DB::table('admins_screen_list')->where('name','Supply Chain  >  Cargo  >  Create Cargo')->delete();
        DB::table('admins_screen_list')->where('name','Supply Chain  >  Cargo  >  Draft Cargo')->delete();
        DB::table('admins_screen_list')->where('name','Supply Chain  >  Cargo  >  Cargo in Transit')->delete();
        DB::table('admins_screen_list')->where('name','Supply Chain  >  Cargo  >  Cargo History')->delete();
        DB::table('admins_screen_list')->where('name','Supply Chain  >  Cargo  >  Quick Receive Cargo')->delete();
        DB::table('admins_screen_list')->where('name','Supply Chain  >  Cargo  >  Quick Receive List')->delete();
        DB::table('admins_screen_list')->where('name','Financials  >  Payments  >  Make Payments')->delete();
        DB::table('admins_screen_list')->where('name','Financials  >  Payments  >  Done Payments')->delete();
        DB::table('admins_screen_list')->where('name','Supply Chain  >  Master Cargo  >  Bag > Pending Shipments for Bag')->delete();
        DB::table('admins_screen_list')->where('name','Supply Chain  >  Master Cargo  >  Runners')->delete();
        DB::table('admins_screen_list')->where('name','Supply Chain  >  Master Cargo  >  Receive Master Cargo')->delete();
        DB::table('admins_screen_list')->where('name','First Mile  >  Pickups  >  Rider Pickup Action Logs')->delete();

        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Shipper > Accounts > Retail Accounts', 'url'=>'admin.retail.accounts.index', 'permission_id' => 428));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'First Mile > Pickups > Pickup Routes', 'url'=>'admin.v2_pickups.pickup_route.index', 'permission_id' => 406));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Master Cargo > Bag > Pending Shipments for Bag', 'url'=>'admin.master_cargo.bag.pending.index', 'permission_id' => 25));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => ' Supply Chain > Master Cargo > Bag > Create Bag', 'url'=>'admin.master_cargo.bag.create.index', 'permission_id' => 26));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Master Cargo > Bag > Bag(s) History', 'url'=>'admin.master_cargo.bag.history.index', 'permission_id' => 124));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Master Cargo > Create Cargo', 'url'=>'admin.master_cargo.create.index', 'permission_id' => 26));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Master Cargo > Quick Receive Bag Shipment(s)', 'url'=>'admin.master_cargo.bag.receive.quick.index', 'permission_id' => 31));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Master Cargo > Master Cargo Received', 'url'=>'admin.master_cargo.receive.index', 'permission_id' => 31));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Runner > Runners On Route', 'url'=>'admin.runner.index', 'permission_id' => 386));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Shipment On-Hold > Supply Chain Shipment On Hold', 'url'=>'admin.cargo.supply_chain.shipment_on_hold.index', 'permission_id' => 404));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Supply Chain > Shipment On-Hold > Supply Chain Shipment On Hold History', 'url'=>'admin.cargo.supply_chain.shipment_on_hold.history.index', 'permission_id' => 405));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Delivery > Delivery Consignee Signature', 'url'=>'admin.delivery.signature.index', 'permission_id' => 441));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Last Mile > Return > Return Confirmed Shipments', 'url'=>'admin.return.confirmed', 'permission_id' => 47));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Financials > COD Payments > Make Payments', 'url'=>'admin.finance.make_payments.index', 'permission_id' => 59));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Financials > COD Payments > Done Payments', 'url'=>'admin.finance.done_payments.index', 'permission_id' => 61));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Financials > COD Payments > Make Payments Pickup Wise', 'url'=>'admin.finance.make_payments_pickup_wise.index', 'permission_id' => 396));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Financials > Retail Payments > Retail Make Payments', 'url'=>'admin.finance.retail.make_payments.index', 'permission_id' => 454));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Financials > Retail Payments > Retail Done Payments', 'url'=>'admin.finance.retail.done_payments.index', 'permission_id' => 455));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > Sales > Key Accounts Dashboard', 'url'=>'admin.settings.sales.key_accounts.dashboard', 'permission_id' => 397));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Support > User Wise Commission', 'url'=>'admin.dashboard.userwise', 'permission_id' => 333));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings > International > International Rates Settings', 'url'=>'admin.settings.international_rates.index', 'permission_id' => 438));
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Human Resource > Active Employees List', 'url'=>'admin.human_resourse.allusers', 'permission_id' => 449));
    }
}
