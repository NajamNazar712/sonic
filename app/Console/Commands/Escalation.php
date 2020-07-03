<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\CRMEscalationController;
use Illuminate\Console\Command;

class Escalation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:escalation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark Crm Launched and In-Process requests Valid/In-valid/Resolved/In-Process';

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
        CRMEscalationController::launched_in_process_requests();
    }
}
