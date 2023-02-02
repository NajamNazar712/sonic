<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use Illuminate\Console\Command;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use App\Http\Models\HR\Employee;

class WeeklyAttendenceSummaryLineManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:weeklyattendencesummary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'weekly attendence summary of employee notification sent to their line manger';

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
        // $line_managers = Employee::where('is_line_manager',1)->where('official_email','!=',null)->get();
        // foreach($line_managers as $line_manager)
        // {
        //     AdminReportsEmailController::weekly_attendence_summary(209,$line_manager->id,$startWeek,$endWeek,$date);

        // }
    }
}
