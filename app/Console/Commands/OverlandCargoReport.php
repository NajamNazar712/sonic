<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class OverlandCargoReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overland:cargo_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Overland Cargo Report';

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
        $date = Carbon::yesterday()->format('Y-m-d');
        $response = AdminReportsEmailController::overnight_overland_cargo($date, 2);
        NotificationsController::send(59, 2, $response);
    }
}
