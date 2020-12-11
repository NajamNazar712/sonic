<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ReceiveDeliveriesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:receivedeliveryreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Receive Delivery Report Link';

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
        $response = AdminReportsEmailController::receive_deliveries($date);
        NotificationsController::send(111, $date, $response);
    }
}
