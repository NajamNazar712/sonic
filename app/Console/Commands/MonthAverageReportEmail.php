<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\Admins\MonthAverageReportsController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MonthAverageReportEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'month:average';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Average Shipments of Month Day Wise Overall';

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
        $date = Carbon::today()->format('Y-m-d');
        $response = MonthAverageReportsController::month_average_overall($date . ' 00:00:00');
        NotificationsController::send(49, $date, $response);
    }
}
