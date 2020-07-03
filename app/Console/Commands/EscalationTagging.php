<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\CRMEscalationController;
use Illuminate\Console\Command;

class EscalationTagging extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:escalationtagging';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crm Request Tagging and Escalation Matrix';

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
        CRMEscalationController::in_process_requests_tagging();
    }
}
