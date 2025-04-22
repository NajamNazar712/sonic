<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;

class QualityOfServiceReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:quality_of_service_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Receive Quality of Service Report';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $setting = DB::table('global_settings')
        ->where('type', 'quality_of_service_report_time')
        ->orWhere('type', 'quality_of_service_report_other_time')
        ->value('setting_value');

        if ($setting == 1) {
            $response = AdminReportsEmailController::qsr_daily_report();
            NotificationsController::send(242, $response);
        }
    }
}
