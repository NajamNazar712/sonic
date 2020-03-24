<?php

namespace App\Console\Commands;

use App\Http\Controllers\CRM\CRMAutomationController;
use Illuminate\Console\Command;

class CRMDelayInDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:delayindelivery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automation CRM Delay in delivery';

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
       dd(CRMAutomationController::automation_delay_in_delivery());
    }
}
