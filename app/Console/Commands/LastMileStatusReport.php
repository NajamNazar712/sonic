<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\LastMileStatusReportController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Console\Command;
use Carbon\Carbon;

class LastMileStatusReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:lastmilestatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $from = Carbon::now()->subHours(2)->startOfHour()->toTimeString();
        $to = Carbon::now()->startOfHour()->toTimeString();

        $response = LastMileStatusReportController::create_report($from, $to);

        NotificationsController::send(147,$response);

    }
}
