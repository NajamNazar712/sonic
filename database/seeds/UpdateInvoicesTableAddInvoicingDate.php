<?php

use Illuminate\Database\Seeder;

class UpdateInvoicesTableAddInvoicingDate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $invoices = DB::table('invoices');

        if ($invoices->exists()) {
        	$invoices = $invoices->whereDate('invoicing_date', '0000-00-00')->get();

        	foreach ($invoices as $invoice) {
        		$date = Carbon::parse($invoice->billing_period_to_date)->subDay()->startOfDay()->toDateString();

        		$invoice->invoicing_date = $date;
        		$invoice->billing_period_to_date = $date;

        		$invoice->save();
        	}
    	}
    }
}
