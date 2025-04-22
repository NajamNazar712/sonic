<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Support\Facades\DB;

class PendingDeliveriesReportNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:pending_deliveries_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Receive Pending Delivery Report';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $setting = DB::table('global_settings')
        ->where('type', 'pending_deliveries_report_time')
        ->value('setting_value');

        if ($setting == 1) {
            $response = AdminReportsEmailController::daily_pending_deliveries();
            NotificationsController::send(241, $response);
        }
    }
}
