<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DailyPickupSalesEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:dailypickupsalescron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Pickup & Sales Report';

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
        $date = Carbon::now();
        AdminReportsController::daily_pickup_sales_report_create(NULL,$date,NULL,NULL);
    }
}
