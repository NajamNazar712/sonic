<?php

use Illuminate\Database\Seeder;

class UpdateGlobalSettingsForInvoiceReturnDeliveredShipper extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = \Carbon\Carbon::now();

        DB::table('global_settings')->insert(array(
            array('type' => 'invoice_against_return_delivered_shipper', 'setting_value' => 0, 'created_at' => $timestamp, 'updated_at' => $timestamp)
        ));
    }
}
