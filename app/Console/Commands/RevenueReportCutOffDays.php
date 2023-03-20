<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminRevenueReportsController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;

class RevenueReportCutOffDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:revenuereportcutoffdays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revenue Report from 1st to 25 days';

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
        //from 1st to 25th days
        $response = AdminRevenueReportsController::revenue_report(2);
        if($response)
        {
            NotificationsController::send(130,$response);
        }
    }
}
