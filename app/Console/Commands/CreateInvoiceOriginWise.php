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
//        $weekStartDate = $now->startOfWeek()->format('Y-m-d H:i');
//        $weekEndDate = $now->endOfWeek()->format('Y-m-d H:i');

        $weekStartDate = $now->subMonths(6)->startOfMonth()->format('Y-m-d H:i');
        $weekEndDate = Carbon::today()->subWeek()->endOfWeek()->format('Y-m-d H:i');
        $invoices = Invoice::whereBetween('invoicing_date', [$weekStartDate, $weekEndDate])->whereNotIn('id', [3012,3024,3054,3059,3098,3119,3163,3171,3205,3226,3266,3270,3304,3342,3388,3392,3395,3426,3455,3462,3474,3502,3510,3521,3522,3536,3560,3589,3629,3636,3652,3653,3666,3686,3708]);
        if($invoices->exists()){
            $invoices = $invoices->get();
            if(count($invoices) > 0){
                foreach ($invoices as $invoice){
                  InvoiceByOriginReportController::create_invoice($invoice);

                }
            }
        }

        $reim_invoice = InvoiceForReimbursement::whereBetween('invoicing_date', [$weekStartDate, $weekEndDate]);
        if($reim_invoice->exists()){
            $reim_invoice = $reim_invoice->get();
            if(count($reim_invoice) > 0){
                foreach ($reim_invoice as $invoice){
                    InvoiceByOriginReportController::create_invoice($invoice);
                }
            }
        }
    }
}
