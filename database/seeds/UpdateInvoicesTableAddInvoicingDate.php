<?php

use Illuminate\Database\Seeder;

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
        var_dump(Carbon::now()->subMonth()->day(28)->startOfDay()->toDateString());
        // var_dump(Carbon::now()->day(1)->subDay()->day(15)->startOfDay()->toDateString());
     //    $invoices = DB::table('invoices');

     //    if ($invoices->exists()) {
     //    	$invoices = $invoices->whereDate('invoicing_date', '0000-00-00')->get();

     //    	foreach ($invoices as $invoice) {
     //    		$date = Carbon::parse($invoice->billing_period_to_date)->subDay()->startOfDay()->toDateString();

     //    		$invoice->invoicing_date = $date;
     //    		$invoice->billing_period_to_date = $date;

     //    		$invoice->save();
     //    	}
    	// }
    }
}
