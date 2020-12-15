<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class OverlandAgingReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:overlandagingreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Overland Aging Report';

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
     /*   $date = Carbon::yesterday()->format('Y-m-d');*/
        $response = AdminReportsEmailController::overland_aging_report();
        NotificationsController::send(112, $response);
    }
}
