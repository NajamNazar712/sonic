<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;
use Carbon\Carbon;
class DebriefingEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:debriefingemail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Debriefing report Hub, Zone and Overall';

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
        $date = Carbon::now()->toDateString();
        AdminReportsController::debriefing_archive_directory();
        $response = AdminReportsController::debriefing_hub_wise_report($date);
        if($response){
            NotificationsController::send(44, $date);
        }
        $response = AdminReportsController::debriefing_zone_wise_report($date);
        if($response){
            NotificationsController::send(45, $date);
        }
        $response = AdminReportsController::debriefing_overall_report($date);
        if($response){
            NotificationsController::send(46, $date);
        }
        
    }
}
