<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PendingDevliveriesEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:pendingdeliveriesreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pending Deliveries Report';

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
        $start_date = Carbon::yesterday()->startOfDay()->addHours(9)->toDateTimeString();
        $end_date = Carbon::today()->startOfDay()->addHours(9)->toDateTimeString();

        $response = AdminReportsEmailController::pending_deliveries_daily_report($start_date, $end_date);
        NotificationsController::send(227, $response);
    }
}
