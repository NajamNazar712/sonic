<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;

class WeeklyAttendanceSummaryLineManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:weeklyattendancesummary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'weekly attendance summary of employee notification sent to their line manger';


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
        $startWeek = Carbon::now()->subWeek()->startOfWeek()->format('Y-m-d'); // 30 May 2022
        $endWeek   = Carbon::now()->subWeek()->endOfWeek()->format('Y-m-d');
        NotificationsController::send(209, $startWeek, $endWeek);
    }
}
