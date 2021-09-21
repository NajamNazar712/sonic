<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;

class RevenueReportMonthlyByDeliveryDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:revenuereportbydeliverydate';

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
        $response = AdminReportsController::revenue_by_delivery_date_excel_download();
        if($response)
        {
            NotificationsController::send(156,null);
        }
    }
}
