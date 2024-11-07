<?php

use App\Http\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateInvoiceShipmentCharges extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   $today = Carbon::today()->toDateString();
        
         $invoices = Invoice::whereDate('created_at',$today)->get();

         foreach($invoices as $invoice){
             foreach ($invoice->invoice_shipments as $invoice_shipment) {
                 $invoice_shipment->update(['invoice_amount' => $invoice_shipment->charges + $invoice_shipment->gst]); 
            }
         }
    }
}
