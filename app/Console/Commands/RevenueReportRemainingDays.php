<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminRevenueReportsController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;

class RevenueReportRemainingDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:revenuereportremainingdays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revenue report from 26th to 31st';

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
        //from 26th to last of month
        $response = AdminRevenueReportsController::revenue_report(3);
        if($response)
        {
            NotificationsController::send(130,$response);
        }
    }
}
