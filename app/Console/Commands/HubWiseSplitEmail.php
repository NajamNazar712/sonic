<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class HubWiseSplitEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubwise:split';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shipments of Hub Day Wise Split';

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
        $response = AdminReportsEmailController::hub_wise_split($date . ' 00:00:00');
        NotificationsController::send(48, $date, $response);
    }
}
