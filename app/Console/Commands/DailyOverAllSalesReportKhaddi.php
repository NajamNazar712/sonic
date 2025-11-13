<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DailyOverAllSalesReportKhaddi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:daily_overall_sales_report_khaddi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Overall sales report for khaddi';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $day = Carbon::yesterday()->toDateString();
        $response = AdminReportsEmailController::overall_sales_report_khaddi($day);
        NotificationsController::send(254, $response);
    }
}
