<?php

namespace App\Console\Commands;

use App\Http\Controllers\NotificationsController;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Http\Models\CRM\CrmRequest;

class CrmProgressReportCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:progress_report';

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
        $crm_complaints = CrmRequest::where('case_nature_id', 1)
        ->whereDate('created_at', '>=', Carbon::today()->subDays(2))
        ->get();


        $crm_service_requests = CrmRequest::where('case_nature_id', 2)
        ->whereDate('created_at', '>=', Carbon::today()->subDays(2))
        ->get();

        $crm_claims = CrmRequest::where('case_nature_id', 4)
        ->whereDate('created_at', '>=', Carbon::today()->subDays(10))
        ->get();


        NotificationsController::send(223, $crm_complaints, $crm_service_requests, $crm_claims);
    }
}
