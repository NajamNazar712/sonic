<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PendingDeliveriesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:pendingdeliveryreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pending Deliveries Report Link';

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
        $response = AdminReportsEmailController::pending_deliveries($date);
        dd($response);
        NotificationsController::send(110, $date, $response);
    }
}
