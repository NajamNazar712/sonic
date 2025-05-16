<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QsrEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:qsrreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quality of Service Report';

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
        $setting = DB::table('global_settings')
        ->where('type', 'quality_of_service_report_time')
        ->orWhere('type', 'quality_of_service_report_other_time')
        ->value('setting_value');

        if ($setting == 1) {
            // $start_date = Carbon::yesterday()->startOfDay()->addHours(10)->toDateTimeString();
            $end_date = Carbon::today()->startOfDay()->addHours(10)->toDateTimeString();

            // $start_date = Carbon::yesterday()->subYear()->toDateTimeString();
            // $end_date = Carbon::today()->toDateTimeString();

            // $response = AdminReportsEmailController::qsr_daily_report($start_date, $end_date);
            $response = AdminReportsEmailController::qsr_daily_report($end_date);
            NotificationsController::send(226, $response);
        }
    }
}
