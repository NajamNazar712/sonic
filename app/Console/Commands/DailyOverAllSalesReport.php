<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admins\AdminReportsEmailController;
use App\Http\Controllers\NotificationsController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DailyOverAllSalesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:daily_overall_sales_report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $setting = DB::table('global_settings')
        ->where('type', 'overall_sales_report_time')
        ->value('setting_value');

        if ($setting == 1){
            $day = Carbon::yesterday()->toDateString();
            $response = AdminReportsEmailController::overall_sales_report($day);
            NotificationsController::send(240, $response);
        }
    }
}
