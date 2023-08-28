<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Http\Models\CRM\CrmRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NotificationsController;

class CrmClosedReasonCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:closed_reason';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This CRON Will Send Email To Those Shipper Whose Status Are Marked As Closed And Shipper Mark Rating As Per Their User Experience.';

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
        $closed_reason = CrmRequest::leftJoin('crm_request_feedbacks', 'crm_requests.id', '=', 'crm_request_feedbacks.crm_request_id')
        ->whereNull('crm_request_feedbacks.crm_request_id')
        ->where('crm_requests.status_id', 4)
        ->whereDate('crm_requests.updated_at', now()->format('Y-m-d'))
        ->select('crm_requests.*')
        ->get();
        
        NotificationsController::send(222, $closed_reason);
    }
}
