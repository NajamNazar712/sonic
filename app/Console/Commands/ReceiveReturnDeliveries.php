<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReceiveReturnDeliveries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:daily_return_received_deliveries_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Report for Return Received Deliveries';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $day = Carbon::yesterday()->toDateString();
        $response = AdminReportsEmailController::return_deliveries_receive($day);
        NotificationsController::send(237, $response);
    }
}
