<?php

namespace App\Console\Commands;

use App\Http\Controllers\Reports\AccountReconciliationController;
use Illuminate\Console\Command;

class AccountReconciliationReportFromStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:reconciliationstart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Accounts Reconciliation Report from 2018';

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
        AccountReconciliationController::reconciliation_old();
    }
}
