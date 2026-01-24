<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminRevenueReportsController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RevenueReportLastMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:revenuereport_lastmonth {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monthly Revenue Report Last Month';

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
        $type = $this->argument('type');
//        $response = AdminRevenueReportsController::revenue_report_last_month($type);
        $response = AdminRevenueReportsController::revenue_report_by_delivery_date_last_month($type);

    }
}
