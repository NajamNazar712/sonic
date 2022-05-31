<?php

use Illuminate\Database\Seeder;

class UpdateAdminScreenForInvoiceReturnDeliveredShipper extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        DB::table('admins_screen_list')->insert( array('created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Settings  >  Invoice Against Return Delivered Shipper', 'url'=>'admin.settings.invoice_against_return_delivered_shipper.index', 'permission_id' => 716));
    }
}
