<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DailyWeightQCReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:daily_weight_qc_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily yesterday weight qc report';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $day = Carbon::yesterday()->toDateString();
        $response = AdminReportsEmailController::weight_qc_report($day);
        NotificationsController::send(239, $response);
    }
}
