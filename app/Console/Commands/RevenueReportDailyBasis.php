<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminRevenueReportsController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;

class RevenueReportDailyBasis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:RevenueReportDailyBasis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revenue Report On Daily Basis';

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
        //on daily basis
        $response = AdminRevenueReportsController::revenue_report(4);
        if($response)
        {
            NotificationsController::send(214,$response);
        }
    }
}
