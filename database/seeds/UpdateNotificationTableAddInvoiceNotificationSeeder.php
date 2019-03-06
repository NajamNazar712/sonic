<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class UpdateNotificationTableAddInvoiceNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');

        DB::table('notifications')->insert(array(
        	array('id' => 27, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Invoice Email to Shipper', 'type_id' => 1, 'subject' => 'Invoice [invoice_number] for Delivered and Returned Shipments (Dated: [billing_period_from_date] - [billing_period_to_date])', 'body' => 'Dear [company_name],' . PHP_EOL . 'This is to notify you that an invoice with the invoice number [invoice_number] has been generated for the charges of your shipments for the period [billing_period_from_date] - [billing_period_to_date].' . PHP_EOL . 'Please find the invoice below and pay through cash/cheque/funds transfer to TRAX Logistics by [due_date].' . PHP_EOL . PHP_EOL . '[invoice]' . PHP_EOL . PHP_EOL . PHP_EOL . 'Regards,' . PHP_EOL . 'TRAX Logistics' . PHP_EOL . 'Address: Plot # 4, DMCHS, Block 7/8, Adjacent to IBL Building Centre,Tipu Sultan Road, Karachi' . PHP_EOL . 'Helpline: +92-3-041-111-232', 'updated_by' => 3, 'status' => 0),
        	array('id' => 28, 'created_at' => $timestamp, 'updated_at' => $timestamp, 'name' => 'Invoice Reminder Email to Shipper', 'type_id' => 1, 'subject' => 'Reminder: Invoice [invoice_number] for Delivered and Returned Shipments (Dated: [billing_period_from_date] - [billing_period_to_date])', 'body' => 'Dear [company_name],' . PHP_EOL . 'This is to notify you that an invoice with the invoice number [invoice_number] has been generated for the charges of your shipments for the period [billing_period_from_date] - [billing_period_to_date].' . PHP_EOL . 'Please find the invoice below and pay through cash/cheque/funds transfer to TRAX Logistics by [due_date].' . PHP_EOL . PHP_EOL . '[invoice]' . PHP_EOL . PHP_EOL . PHP_EOL . 'Regards,' . PHP_EOL . 'TRAX Logistics' . PHP_EOL . 'Address: Plot # 4, DMCHS, Block 7/8, Adjacent to IBL Building Centre,Tipu Sultan Road, Karachi' . PHP_EOL . 'Helpline: +92-3-041-111-232', 'updated_by' => 3, 'status' => 0)
        ));
    }
}
