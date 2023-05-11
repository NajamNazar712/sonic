<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\InvoiceByOriginReportController;
use App\Http\Models\Admin\RevenueByInvoiceReport;
use App\Http\Models\Invoice;
use App\Http\Models\InvoiceForReimbursement;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateInvoiceOriginWise extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:revenueoriginwise';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revenue Report By Invoice';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now();
        $weekStartDate = $now->startOfWeek()->format('Y-m-d H:i');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d H:i');

        $invoices = Invoice::whereBetween('created_at', [$weekStartDate, $weekEndDate]);
        if($invoices->exists()){
            $invoices = $invoices->get();
            if(count($invoices) > 0){
                foreach ($invoices as $invoice){
                    InvoiceByOriginReportController::create_invoice($invoice);
                }
            }
        }

        $reim_invoice = InvoiceForReimbursement::whereBetween('created_at', [$weekStartDate, $weekEndDate]);
        if($reim_invoice->exists()){
            $reim_invoice = $reim_invoice->get();
            if(count($reim_invoice) > 0){
                foreach ($reim_invoice as $invoice){
                    InvoiceByOriginReportController::create_invoice($invoice);
                }
            }
        }
//        InvoiceByOriginReportController::update_invoices();
    }
}
