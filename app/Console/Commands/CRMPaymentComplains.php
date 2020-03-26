<?php

namespace App\Console\Commands;

use App\Http\Controllers\CRM\CRMAutomationController;
use Illuminate\Console\Command;

class CRMPaymentComplains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:paymentcomplainautomation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        CRMAutomationController::automation_payment_complains();
    }
}
