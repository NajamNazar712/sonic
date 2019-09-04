<?php

use Illuminate\Database\Seeder;

use App\Http\Models\Invoice;

use Carbon\Carbon;

class UpdateInvoicesTableAddInvoicingDate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $invoices = Invoice::whereDate('invoicing_date', '0000-00-00');

        if ($invoices->exists()) {
        	$invoices = $invoices->get();

        	foreach ($invoices as $invoice) {
        		$date = Carbon::parse($invoice->billing_period_to_date)->subDay()->startOfDay()->toDateString();

        		$invoice->invoicing_date = $date;
        		$invoice->billing_period_to_date = $date;

        		$invoice->save();
        	}
    	}
    }
}
