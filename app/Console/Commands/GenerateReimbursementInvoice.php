<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminFinanceController;
use Illuminate\Console\Command;

class GenerateReimbursementInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reimbursement_invoice:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reimbursement Invoice Generate';

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
        AdminFinanceController::generate_reimbursement_invoice();
    }
}
