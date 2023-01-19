<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminRevenueReportsController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;

class RevenueReportMonthlyByDeliveryDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:revenuereportbydeliverydate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monthly Revenue Report By Delivery / Return Date';

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
        //from 1st to last
        $response = AdminRevenueReportsController::revenue_report_by_delivery_date(1);
        if($response)
        {
            NotificationsController::send(156,$response);
        }
    }
}
