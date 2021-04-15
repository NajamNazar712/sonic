<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;

class RevenueReportMonthlyEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:revenuereport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monthly Revenue Report';

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
        $response = AdminReportsController::revenue_excel_download();
        if($response)
        {
            NotificationsController::send(130,null);
        }
    }
}
