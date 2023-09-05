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
        $crm_progress_report = CrmRequest::whereIn('case_nature_id', [1,2,4])
        ->where('agent_id','!=','')
        ->where('updated_at','>=', Carbon::now()->subDay())
        ->get();

        NotificationsController::send(223, $crm_progress_report);
    }
}
