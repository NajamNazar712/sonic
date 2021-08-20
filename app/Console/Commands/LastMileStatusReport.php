<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\LastMileStatusReportController;
use App\Http\Controllers\NotificationsController;
use App\Http\Models\Admin\GlobalSettings;
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
    protected $description = 'Last Mile Status Report';

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
        $time = Carbon::now()->format('H:i');
        $end_of_day = Carbon::today()->endOfDay()->format('H:i');

        if(($time > '10:00') && ($time < $end_of_day) ){
            $settings = GlobalSettings::where('type', 'last_mile_cron_time');

            if ($settings->exists()) {
                $settings = $settings->first();

                $time = $settings->setting_value;

            }
            else{
                $time = 2;
            }
            $from = Carbon::now()->subHours($time)->startOfHour()->toTimeString();
            $to = Carbon::now()->startOfHour()->toTimeString();

            $response = LastMileStatusReportController::create_report($from, $to);

            NotificationsController::send(147,$response);
        }


    }
}
