<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DailyPickupSalesRMEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:dailypickupsalesreportrm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Daily Pickup & Sales Report Email to Regional Managers';

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
        $date = Carbon::yesterday()->format('Y-m-d');
        $response = AdminReportsController::daily_pickup_sales_report_create(NULL, $date . ' 00:00:00', NULL, FALSE);
        NotificationsController::send(26,$date,$response);
    }
}
